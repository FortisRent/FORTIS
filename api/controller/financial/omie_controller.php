<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OmieController
{
  private string $key;
  private string $secret;
  private string $baseUrl;
  private Client $client;

  public function __construct()
  {
    $this->key = $_ENV['OMIE_APP_KEY'];
    $this->secret = $_ENV['OMIE_APP_SECRET'];
    $this->baseUrl = $_ENV['OMIE_BASE_URL'];
    $this->client = new Client([
      'base_uri' => $this->baseUrl,
      'timeout' => 60.0,
      'headers' => ['Content-Type' => 'application/json']
    ]);
  }

  private function post(string $endpoint, array $body): array
  {
    try {
      $response = $this->client->post($endpoint, [
        'json' => $body
      ]);
      return json_decode($response->getBody()->getContents(), true);
    } catch (RequestException $e) {
      // Se tem resposta da Omie, captura o corpo
      if ($e->hasResponse()) {
        $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
        return $responseBody ?? [];
      }
      // Se não tem resposta, retorna erro genérico
      error_log('Erro na requisição POST: ' . $e->getMessage());
      return [];
    }
  }


  public function get_clients(): void
  {
    $allClients = [];
    $pagina = 1;
    $totalPaginas = 1;

    do {
      $body = [
        "call" => "ListarClientes",
        "app_key" => $this->key,
        "app_secret" => $this->secret,
        "param" => [
          [
            "pagina" => $pagina,
            "registros_por_pagina" => 50,
            "apenas_importado_api" => "N",
          ]
        ]
      ];

      $response = $this->post("geral/clientes/", $body);

      if (empty($response)) {
        throw new Exception("Falha ao obter clientes da API Omie na página {$pagina}.");
      }

      $clientes = $response['clientes_cadastro'] ?? [];
      $allClients = array_merge($allClients, $clientes);

      $totalPaginas = $response['total_de_paginas'] ?? 1;
      $pagina++;
    } while ($pagina <= $totalPaginas);

    echo json_encode(["clients" => $allClients]);
  }

  public function create_client(): void
  {
    $input = file_get_contents("php://input");
    $clientData = json_decode($input, true);

    if (!$clientData) {
      http_response_code(400);
      echo json_encode(["error" => "Dados JSON inválidos."]);
      return;
    }

    $clientUuid = uniqid();

    $body = [
      "call" => "IncluirCliente",
      "app_key" => $this->key,
      "app_secret" => $this->secret,
      "param" => [
        [
          "codigo_cliente_integracao" => $clientUuid,
          "razao_social" => $clientData['razao_social'],
          "nome_fantasia" => $clientData['nome_fantasia'],
          "cnpj_cpf" => $clientData['cnpj_cpf'],
          "cep" => $clientData['cep'],
          "pesquisar_cep" => "S",
          "endereco_numero" => $clientData['numero'],
          "complemento" => $clientData['complemento'],
          "email" => $clientData['email'],
          "optante_simples_nacional" => $clientData['optante_simples_nacional'],
          "contribuinte" => $clientData['contribuinte'],
        ]
      ]
    ];

    $response = $this->post('geral/clientes/', $body);

    if (empty($response)) {
      http_response_code(500);
      echo json_encode(["error" => "Falha ao criar cliente na API Omie."]);
      return;
    }

    if (isset($response['faultcode'])) {
      http_response_code(409); // Conflito (cliente já existe, por exemplo)
      echo json_encode([
        "error" => $response['faultstring'] ?? 'Erro desconhecido na Omie',
        "faultcode" => $response['faultcode']
      ]);
      return;
    }

    echo json_encode(["created_client" => $response]);
  }

  public function create_cobranca(): void
  {
    $input = file_get_contents("php://input");
    $cobrancaData = json_decode($input, true);

    if (!$cobrancaData) {
      http_response_code(400);
      echo json_encode(["error" => "Dados JSON inválidos."]);
      return;
    }

    $emailsStr = !empty($cobrancaData['emails']) ? implode(';', $cobrancaData['emails']) : '';
    $cobrancaUuid = uniqid();

    $body = [
      "call" => "IncluirOS",
      "app_key" => $this->key,
      "app_secret" => $this->secret,
      "param" => [
        [
          "Cabecalho" => [
            "cCodIntOS" => $cobrancaUuid,
            "cCodParc" => "000",
            "cEtapa" => "50",
            "dDtPrevisao" => $cobrancaData['data_vencimento'],
            "nCodCli" => $cobrancaData['codigo_cliente_fornecedor'],
            "nQtdeParc" => 1
          ],
          "Departamentos" => [],
          "Email" => [
            "cEnvRecibo" => "S",
            "cEnvBoleto" => "N",
            "cEnviarPara" => $emailsStr
          ],
          "InformacoesAdicionais" => [
            "cCidPrestServ" => "FLORIANOPOLIS (SC)",
            "cCodCateg" => "1.01.02",
            "nCodCC" => 2405145089
          ],
          "ServicosPrestados" => [
            [
              "cCodServLC116" => "7.07",
              "cCodServMun" => "01015",
              "cDadosAdicItem" => "Serviços prestados (API)",
              "cDescServ" => trim($cobrancaData['description']),
              "cRetemISS" => "N",
              "cTribServ" => "01",
              "nQtde" => 1,
              "nValUnit" => (float) $cobrancaData['valor_documento']
            ]
          ]
        ]
      ]
    ];

    $response = $this->post('servicos/os/', $body);

    if (isset($response['faultcode'])) {
      http_response_code(500);
      echo json_encode([
        "error" => $response['faultstring'] ?? 'Erro desconhecido na Omie',
        "faultcode" => $response['faultcode']
      ]);
      return;
    }

    $cCodIntOS = $response['cCodIntOS'];
    $nCodOS = $response['nCodOS'];

    $fatura_body = [
      "call" => "FaturarOS",
      "app_key" => $this->key,
      "app_secret" => $this->secret,
      "param" => [
        [
          "cCodIntOS" => $cCodIntOS,
          "nCodOS" => $nCodOS,
        ]
      ]
    ];

    $fatura_response = $this->post('servicos/osp/', $fatura_body);

    if (isset($fatura_response['faultcode'])) {
      http_response_code(500);
      echo json_encode([
        "error" => $fatura_response['faultstring'] ?? 'Erro desconhecido na Omie',
        "faultcode" => $fatura_response['faultcode']
      ]);
      return;
    }

    echo json_encode([
      "created_cobranca" => $response,
      "fatura" => $fatura_response,
    ]);
  }

  public function create_boleto(): void
  {
    $input = file_get_contents("php://input");
    $boletoData = json_decode($input, true);

    if (!$boletoData) {
      http_response_code(400);
      echo json_encode(["error" => "Dados JSON inválidos."]);
      return;
    }

    $emailsStr = !empty($boletoData['emails']) ? implode(';', $boletoData['emails']) : '';
    $boletoUuid = uniqid();

    $body = [
      "call" => "IncluirOS",
      "app_key" => $this->key,
      "app_secret" => $this->secret,
      "param" => [
        [
          "Cabecalho" => [
            "cCodIntOS" => $boletoUuid,
            "cCodParc" => "000",
            "cEtapa" => "50",
            "dDtPrevisao" => $boletoData['data_vencimento'],
            "nCodCli" => $boletoData['codigo_cliente_fornecedor'],
            "nQtdeParc" => 1
          ],
          "Departamentos" => [],
          "Email" => [
            "cEnvRecibo" => "S",
            "cEnvBoleto" => "S",
            "cEnviarPara" => $emailsStr
          ],
          "InformacoesAdicionais" => [
            "cCidPrestServ" => "FLORIANOPOLIS (SC)",
            "cCodCateg" => "1.01.02",
            "nCodCC" => 2405145089
          ],
          "ServicosPrestados" => [
            [
              "cCodServLC116" => "7.07",
              "cCodServMun" => "01015",
              "cDadosAdicItem" => "Serviços prestados (API)",
              "cDescServ" => trim($boletoData['description']),
              "cRetemISS" => "N",
              "cTribServ" => "01",
              "nQtde" => 1,
              "nValUnit" => (float) $boletoData['valor_documento']
            ]
          ]
        ]
      ]
    ];

    $response = $this->post('servicos/os/', $body);

    if (isset($response['faultcode'])) {
      http_response_code(500);
      echo json_encode([
        "error" => $response['faultstring'] ?? 'Erro desconhecido na Omie',
        "faultcode" => $response['faultcode']
      ]);
      return;
    }

    $cCodIntOS = $response['cCodIntOS'];
    $nCodOS = $response['nCodOS'];

    $fatura_body = [
      "call" => "FaturarOS",
      "app_key" => $this->key,
      "app_secret" => $this->secret,
      "param" => [
        [
          "cCodIntOS" => $cCodIntOS,
          "nCodOS" => $nCodOS,
        ]
      ]
    ];

    $fatura_response = $this->post('servicos/osp/', $fatura_body);

    if (isset($fatura_response['faultcode'])) {
      http_response_code(500);
      echo json_encode([
        "error" => $fatura_response['faultstring'] ?? 'Erro desconhecido na Omie',
        "faultcode" => $fatura_response['faultcode']
      ]);
      return;
    }

    echo json_encode([
      "created_cobranca" => $response,
      "fatura" => $fatura_response,
    ]);
  }
}