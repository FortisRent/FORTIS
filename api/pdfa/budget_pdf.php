<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../db/db.php';



class BudgetPdfController {
    private $mysqli;

    public function __construct($mysqli){
        $this->mysqli = $mysqli;
    }

    /* ----------------- helpers de moeda ----------------- */
    private function brl(float $v): string {
        return number_format($v, 2, ',', '.');
    }
    private function fromBrl(string $s): float {
        return (float) str_replace(['.', ','], ['', '.'], $s);
    }

    public function data_pdf2($budget_uuid) {
        $token = validate_token();

        $this->mysqli->begin_transaction();

        $query = "SELECT id FROM budget WHERE uuid = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("s", $budget_uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Nenhum requerimento encontrado para o UUID fornecido.");
        }

        $budget_id = $result->fetch_assoc()['id'];

        try {
            // Dados principais
            $budget_list = $this->mysqli->query("SELECT b.uuid AS budget_uuid, u.full_name AS user_name, u.cpf AS user_cpf,
                                                        c.name AS client_name, c.cpf AS cnpj, p.name AS project_name,
                                                        (b.total_fee/100) AS total_fee, (b.amount/100) AS amount, p.identifier,
                                                        DATE_FORMAT(b.created_at, '%d de %M de %y') AS created_at
                                                   FROM budget b
                                                   INNER JOIN project p ON b.project_id = p.id
                                                   LEFT JOIN project_user pu ON pu.project_id = p.id
                                                   LEFT JOIN user u ON pu.user_id = u.id
                                                   LEFT JOIN project_client pc ON pc.project_id = p.id
                                                   LEFT JOIN client c ON pc.client_id = c.id
                                                  WHERE b.id = $budget_id AND b.deleted_at IS NULL");

            $budget_charge_list = $this->mysqli->query("SELECT sc.name AS service_charge_name, (sc.amount / 100) AS fee_amount
                                                          FROM budget_service_charge bsc
                                                          INNER JOIN service_charge sc ON bsc.service_charge_id = sc.id
                                                          WHERE bsc.budget_id = $budget_id AND bsc.deleted_at IS NULL");

            $query_budget_machine = "SELECT bm.id AS budget_machine_id, b.uuid AS budget_uuid, m.name AS machine_name,
                                            c.name AS company_name, (mf.price_per_hour / 100) AS price_per_hour,
                                            mf.minimum_rental_period, (mf.price_per_distance / 100) AS price_per_distance,
                                            mf.distance_amount, mf.special_hour_fee, mf.observation,
                                            DATE_FORMAT(b.created_at, '%d/%m/%Y') AS buget_created_at
                                      FROM budget_machine bm
                                      INNER JOIN budget b ON bm.budget_id = b.id
                                      INNER JOIN machine m ON bm.machine_id = m.id
                                      LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
                                      LEFT JOIN company c ON b.company_id = c.id
                                      WHERE b.id = $budget_id AND bm.deleted_at IS NULL";

            $stmt_budget_machine = $this->mysqli->prepare($query_budget_machine);
            $stmt_budget_machine->execute();
            $result_budget_machine = $stmt_budget_machine->get_result();

            $machines = [];
            while ($row = $result_budget_machine->fetch_assoc()) {
                $machine_price = $row['price_per_hour'] * $row['minimum_rental_period'];

                $row['TOTAL_MINIMUM_RENTAL_PRICE'] = number_format($machine_price, 2, ',', '.');
                $row['MINIMUM_RENTAL_PERIOD'] = number_format($row['minimum_rental_period'], 2, ',', '.');
                $row['MINIMUM_RENTAL_PRICE'] = number_format($machine_price, 2, ',', '.');
                $row['price_per_hour'] = number_format($row['price_per_hour'], 2, ',', '.');

                $machines[] = $row;
            }

            // Operadores
            $stmt_employees = $this->mysqli->prepare("SELECT COUNT(*) AS total_operators,
                                                               (r.hourly_price / 100) AS hour_price,
                                                               r.name AS role_name,
                                                               mf.minimum_rental_period
                                                        FROM budget_machine_operator bmo
                                                        INNER JOIN `role` r ON bmo.role_id = r.id
                                                        INNER JOIN budget_machine bm ON bmo.budget_machine_id = bm.id
                                                        INNER JOIN machine m ON bm.machine_id = m.id
                                                        INNER JOIN machine_franchise mf ON mf.machine_id = m.id
                                                        INNER JOIN budget b ON bm.budget_id = b.id
                                                        WHERE b.id = ? AND bmo.deleted_at IS NULL
                                                        GROUP BY r.id");
            $stmt_employees->bind_param('i', $budget_id);
            $stmt_employees->execute();
            $result_employees = $stmt_employees->get_result();

            $aux_operators_data = [];
            while ($row = $result_employees->fetch_assoc()) {
                $total_operators = $row['total_operators'] ?? 0;
                $hour_price      = $row['hour_price'] ?? 0;
                $role_name       = $row['role_name'] ?? 'Desconhecido';
                $min_hours       = $row['minimum_rental_period'] ?? 0;
                $total_price     = $total_operators * $hour_price * $min_hours;

                $aux_operators_data[] = [
                    'QTD'         => $total_operators,
                    'ROLE'        => $role_name,
                    'PRICE'       => number_format($hour_price, 2, ',', '.'),
                    'HOURS'       => $min_hours,
                    'TOTAL'       => number_format($total_price, 2, ',', '.'),
                    'TOTAL_FLOAT' => $total_price
                ];
            }

            $budget_data    = $budget_list->fetch_assoc();
            $budget_charges = [];
            while ($charge = $budget_charge_list->fetch_assoc()) {
                $budget_charges[] = [
                    'SERVICE_CHARGE_NAME' => $charge['service_charge_name'],
                    'FEE_AMOUNT'          => number_format($charge['fee_amount'], 2, ',', '.')
                ];
            }

            $this->mysqli->commit();

            return [
                'IDENTIFIER'     => $budget_data['identifier'] ?? null,
                'CREATED_AT'     => $budget_data['created_at'] ?? null,
                'NAME'           => $budget_data['client_name'] ?? $budget_data['user_name'],
                'DOCUMENT'       => $budget_data['cnpj'] ?? $budget_data['user_cpf'],
                'PROJECT_NAME'   => $budget_data['project_name'] ?? 'Projeto sem nome',
                'MACHINES'       => $machines ?? [],
                'SERVICE_CHARGE' => $budget_charges ?? [],
                'AUX_OPERATORS'  => $aux_operators_data ?? []
            ];
        } catch (Exception $e) {
            $this->mysqli->rollback();
            throw new Exception("Erro ao processar dados: " . $e->getMessage());
        }
    }

    public function export_budget_data_to_pdfa($data) {
        $output_dir = __DIR__ . '/output';
        if (!is_dir($output_dir)) mkdir($output_dir, 0777, true);

        $filename    = 'proposta_comercial_' . ($data['IDENTIFIER'] ?? 'sem_identificador') . '.pdf';
        $output_path = $output_dir . '/' . $filename;

        $pdf = new \TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Proposta Comercial_' . ($data['IDENTIFIER'] ?? ''));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 18, 12, true);
        $pdf->AddPage();

        // Paleta
        $black      = [17,17,17];
        $darkGrey   = [42,42,42];
        $midGrey    = [230,230,230];
        $lightGrey  = [245,245,245];

        // Fonte com acentos
        $pdf->SetFont('dejavusans', '', 10, '', true);

        // Logos (ajuste caminhos)
       $logoLeft  = realpath(__DIR__ . '/../../mobile/src/assets/logo-fortis.svg'); 
        $logoRight = realpath(__DIR__ . '/../../mobile/src/assets/pa-carregadeira.png');
        if ($logoLeft && file_exists($logoLeft)) {
            $pdf->ImageSVG(
                $file = $logoLeft,
                $x = 15, 
                $y = 13, 
                $w = 40, 
                $h = 0, 
                $link = '', 
                $align = '', 
                $palign = '', 
                $border = 0, 
                $fitonpage = false
            );
        }
        if (is_file($logoRight)) $pdf->Image($logoRight, 155, 13, 40, 0, 'PNG');

       // Linha preta acima do título
        $pdf->SetDrawColor($black[0], $black[1], $black[2]);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, 38, 200, 38);
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0,0,0);

        $pdf->SetXY(10, 40);
        $pdf->SetFont('dejavusans', 'B', 16, '', true);
        $pdf->Cell(190, 10, 'PROPOSTA COMERCIAL', 0, 1, 'C');


        // Faixa Cliente / Documento
        $pdf->Ln(6);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('dejavusans','B',11,'',true);
        $pdf->Cell(125, 12, strtoupper($data['NAME'] ?? ''), 1, 0, 'C', true);
        $pdf->Cell(65, 12, $data['DOCUMENT'] ?? '', 1, 1, 'C', true);

        // Seção SERVIÇO
        $pdf->Ln(4);
        $pdf->SetFont('dejavusans','B',11,'',true);
        $pdf->SetTextColor($black[0],$black[1],$black[2]);
        $pdf->Cell(0, 7, 'SERVIÇO', 0, 1, 'L');
        $pdf->SetFont('dejavusans','',9,'',true);
        $pdf->MultiCell(0, 6,
            'LOCAÇÃO SPOT DE GUINDASTE ARTICULADO (MUNCK) E FERRAMENTAS DE REMOÇÃO PARA MOVIMENTAÇÃO DE MÁQUINAS' .
            (empty($data['PROJECT_NAME']) ? '' : " | REF: " . $data['IDENTIFIER']),
            0, 'L');

        // Tabela de orçamento
        $pdf->Ln(4);
        $pdf->SetFont('dejavusans','B',11,'',true);
        $pdf->Cell(0, 8, 'TABELA DE ORÇAMENTO', 0, 1, 'L');

        // Header
        $pdf->SetFont('dejavusans','',9,'',true);

        // borda fina e discreta
        $pdf->SetLineStyle([
            'width' => 0.15,              // tente 0.15 ou 0.10 mm
            'cap'   => 'butt',
            'join'  => 'miter',
            'dash'  => 0,
            'color' => [80, 80, 80]       // cinza escuro (opcional)
        ]);

        // $pdf->SetFillColor(240,240,240);  // fundo do header (se quiser)
        $pdf->Cell(15, 9, 'Item',       'LTRB', 0, 'C', true);
        $pdf->Cell(17, 9, 'Qnt',        'LTRB', 0, 'C', true);
        $pdf->Cell(90, 9, 'Descrição',  'LTRB', 0, 'C', true);
        $pdf->Cell(30, 9, 'Valor Hora', 'LTRB', 0, 'C', true);
        $pdf->Cell(20, 9, 'Horas',      'LTRB', 0, 'C', true);
        $pdf->Cell(22, 9, 'Valor',      'LTRB', 1, 'C', true);


        $pdf->SetFont('dejavusans','',9,'',true);

        $item_count          = 1;
        $valor_total_minimo  = 0.0;

        // Máquinas
        foreach (($data['MACHINES'] ?? []) as $machine) {
            $qtde       = 1;
            $descricao  = $machine['machine_name'];
            $valorHoraS = $machine['price_per_hour'];
            $horasS     = $machine['minimum_rental_period'];
            $valorMinS  = $machine['MINIMUM_RENTAL_PRICE'];

            $valorMinF  = (float) str_replace(['.', ','], ['', '.'], $valorMinS);
            $valor_total_minimo += $valorMinF;

            $pdf->SetLineStyle(['width'=>0.15, 'color'=>[80,80,80]]);

            $pdf->Cell(15, 9, $item_count++, 1, 0, 'C');
            $pdf->Cell(17, 9, $qtde, 1, 0, 'C');
            $pdf->Cell(90, 9, $descricao, 1, 0, 'L');
            $pdf->Cell(30, 9, 'R$ ' . $valorHoraS, 1, 0, 'C');
            $pdf->Cell(20, 9, $horasS, 1, 0, 'C');
            $pdf->Cell(22, 9, $valorMinS, 1, 1, 'C');
        }

        // Operadores auxiliares (entram na mesma tabela)
        if (!empty($data['AUX_OPERATORS'])) {
            foreach ($data['AUX_OPERATORS'] as $op) {
                $valor_total_minimo += (float) $op['TOTAL_FLOAT'];

                $pdf->SetLineStyle(['width'=>0.15, 'color'=>[80,80,80]]);

                $pdf->Cell(15, 9, $item_count++, 1, 0, 'C');
                $pdf->Cell(17, 9, $op['QTD'], 1, 0, 'C');
                $pdf->Cell(90, 9, $op['ROLE'], 1, 0, 'L');
                $pdf->Cell(30, 9, 'R$ ' . $op['PRICE'], 1, 0, 'C');
                $pdf->Cell(20, 9, $op['HOURS'], 1, 0, 'C');
                $pdf->Cell(22, 9, $op['TOTAL'], 1, 1, 'C');
            }
        }

        // Encargos como linhas também (colunas de hora/horas ficam "-")
        $valor_total_encargos = 0.0;
        if (!empty($data['SERVICE_CHARGE'])) {
            foreach ($data['SERVICE_CHARGE'] as $charge) {
                $v = $this->fromBrl($charge['FEE_AMOUNT']);
                $valor_total_encargos += $v;

                $pdf->SetLineStyle(['width'=>0.15, 'color'=>[80,80,80]]);
                
                $pdf->Cell(15, 9, $item_count++, 1, 0, 'C');
                $pdf->Cell(17, 9, 1, 1, 0, 'C');
                $pdf->Cell(90, 9, $charge['SERVICE_CHARGE_NAME'], 1, 0, 'L');
                $pdf->Cell(30, 9, '-', 1, 0, 'C');
                $pdf->Cell(20, 9, '-', 1, 0, 'C');
                $pdf->Cell(22, 9, $charge['FEE_AMOUNT'], 1, 1, 'C');
            }
        }

        // Valor total
        $pdf->SetFont('dejavusans','',9.5,'',true);
        $pdf->SetFillColor($black[0], $black[1], $black[2]);
        $pdf->SetTextColor(255,255,255);
        $total_geral = $valor_total_minimo + $valor_total_encargos;
        $pdf->Cell(172, 12, 'Valor Minimo Total', 1, 0, 'R', true);
        $pdf->SetFont('dejavusans','B',9.5,'',true);
        $pdf->Cell(22, 12, 'R$ ' . $this->brl($total_geral), 1, 1, 'C', true);
        $pdf->SetTextColor(0,0,0);

        // CONDIÇÕES GERAIS
        $pdf->Ln(6);
        $pdf->SetFont('dejavusans','B',11,'',true);
        $pdf->Cell(0, 8, 'CONDIÇÕES GERAIS', 0, 1, 'L');
        $pdf->SetFont('dejavusans','',9,'',true);
        $bullets = [
            'A CONTRATANTE ficará responsável pela liberação da entrada do veículo e do motorista na área onde será realizado o serviço...',
            'Os tempos demandados para integração, check list e atividades assemelhadas serão considerados horas à disposição e cobradas em medição.',
            'Na impossibilidade de execução do serviço por motivo não imputável à contratada, permanecem os valores e condições de pagamento desta proposta.',
            'A contagem mínima das horas normais é entre 7:00h e 18:00h (seg a sex).',
            'Horas fora do horário normal, finais de semana e feriados são consideradas Horas Especiais com adicional de 30%.',
            'Período de integração ou treinamento na obra contará como hora trabalhada.'
        ];
        foreach ($bullets as $b) {
            $pdf->SetFont('dejavusans','B',9,'',true);
            $pdf->Write(0, '• ');
            $pdf->SetFont('dejavusans','',9,'',true);
            $pdf->MultiCell(0, 6, $b, 0, 'L');
        }

        // Validade
        $pdf->Ln(4);
        $pdf->SetFont('dejavusans','B',10,'',true);
        $pdf->Write(0, 'VALIDADE DA PROPOSTA – ');
        $pdf->SetFont('dejavusans','',9,'',true);
        $pdf->Write(0, 'A presente proposta tem validade de 30 (Trinta) dias a contar da presente data.');

        // Rodapé preto com contatos
        $pdf->SetY(-28);
        $pdf->SetFillColor($black[0], $black[1], $black[2]);
        $pdf->Rect(10, $pdf->GetY(), 190, 18, 'F');

        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('dejavusans','B',9,'',true);
        $companyLine = 'RODOBRAS GUINDASTES · 01.000.000/0001-01';
        $pdf->SetXY(12, $pdf->GetY()+3);
        $pdf->Cell(120, 6, $companyLine, 0, 0, 'L');

        $pdf->SetFont('dejavusans','',9,'',true);
        $pdf->Cell(68, 6, 'Gerado em: ' . date('d/m/Y H:i'), 0, 1, 'R');

        $pdf->SetXY(12, $pdf->GetY());
        $pdf->Cell(120, 6, 'COMERCIAL@RODOBRASGUINDASTES.COM.BR', 0, 0, 'L');
        $pdf->Cell(68, 6, '(48) 99158-2727  ·  (48) 3285-2727', 0, 1, 'R');

        // Reset cor texto
        $pdf->SetTextColor(0,0,0);

        $pdf->Output($output_path, 'F');
        return '/pdfa/output/' . $filename;
    }

    public function generate_pdf($budget_uuid)
    {
        $data = $this->data_pdf2($budget_uuid);
        $outputFile = $this->export_budget_data_to_pdfa($data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["output" => $outputFile]);
    }
}
