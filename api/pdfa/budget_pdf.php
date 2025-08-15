<?php

    require_once 'vendor/autoload.php';
    require_once __DIR__ . '/../db/db.php';

class BudgetPdfController {
    private $mysqli;

    public function __construct($mysqli){
        $this->mysqli = $mysqli;
    }

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

            $budget_charge_list = $this->mysqli->query("SELECT sc.name AS service_charge_name, (bsc.fee_amount / 100) AS fee_amount
                                                          FROM budget_service_charge bsc
                                                          INNER JOIN service_charge sc ON bsc.service_charge_id = sc.id
                                                          WHERE bsc.budget_id = $budget_id AND bsc.deleted_at IS NULL");

            $query_budget_machine = "SELECT bm.id AS budget_machine_id, b.uuid AS budget_uuid, m.name AS machine_name,
                                            c.name AS company_name, (bm.price_per_hour / 100) AS price_per_hour,
                                            bm.minimum_rental_period, (bm.price_per_distance / 100) AS price_per_distance, b.total_distance,
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
                $machine_distance_price = $row['total_distance'] * $row['distance_amount'];

                $row['TOTAL_MINIMUM_RENTAL_PRICE'] = number_format($machine_price, 2, ',', '.');
                $row['MINIMUM_RENTAL_PERIOD'] = number_format($row['minimum_rental_period'], 2, ',', '.');
                $row['MINIMUM_RENTAL_PRICE'] = number_format($machine_price, 2, ',', '.');
                $row['price_per_hour'] = number_format($row['price_per_hour'], 2, ',', '.');
                $row['MACHINE_DISTANCE_PRICE'] = number_format($machine_distance_price, 2, ',', '.');

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
        $pdf->SetAutoPageBreak(true, 22); // reserva espaço p/ rodapé (>= altura do footer)
        $pdf->AddPage();

        // Paleta
        $black = [0,0,0];
        $darkGrey   = [42,42,42];
        $midGrey    = [230,230,230];
        $lightGrey  = [245,245,245];

        $pdf->SetFont('dejavusans', '', 10, '', true);

        // Logos (ajuste caminhos)
       $logoLeft  = realpath(__DIR__ . '/../../mobile/src/assets/logo-fortis.svg'); 
        $logoRight = realpath(__DIR__ . '/LOGO-RODOBRAS.jpg');
        if ($logoLeft && file_exists($logoLeft)) {
            $pdf->ImageSVG(
                $file = $logoLeft,
                $x = 20, 
                $y = 7, 
                $w = 50, 
                $h = 0, 
                $link = '', 
                $align = '', 
                $palign = '', 
                $border = 0, 
                $fitonpage = false
            );
        }
        if (is_file($logoRight)) $pdf->Image($logoRight, 115, 8, 75, 0, 'JPG');

        // Linha preta acima do título
        $lineLength = 170;
        $pageWidth  = $pdf->getPageWidth();
        $startX     = ($pageWidth - $lineLength) / 2;
        $endX       = $startX + $lineLength;

        $pdf->SetDrawColor($black[0], $black[1], $black[2]);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($startX, 30, $endX, 30);
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0,0,0);

        $pdf->SetXY(10, 33);
        $pdf->SetFont('dejavusans', 'B', 17, '', true);
        $pdf->Cell(190, 10, 'PROPOSTA COMERCIAL', 0, 1, 'C');

        // Faixa Cliente / Documento
        $pdf->Ln(3);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('dejavusans','',13,'',true);

        $totalWidth = 100 + 65; 
        $pageWidth  = $pdf->getPageWidth();
        $margins    = $pdf->getMargins();
        $usableWidth = $pageWidth - $margins['left'] - $margins['right'];


        $startX = $margins['left'] + ($usableWidth - $totalWidth) / 2;
        $pdf->SetX($startX);

        $pdf->Cell(100, 15, $data['NAME'] ?? '', 1, 0, 'C', true);
        $pdf->Cell(65, 15, $data['DOCUMENT'] ?? '', 1, 1, 'C', true);

        // SERVIÇO
        $pdf->Ln(4);
        $pdf->SetFont('dejavusans','B',12,'',true);
        $pdf->SetTextColor($black[0],$black[1],$black[2]);
        $pdf->SetXY($startX, $pdf->GetY());
        $pdf->Cell($endX, 7, 'PROJETO', 0, 1, 'L');
        $pdf->SetFont('dejavusans','',9,'',true);
        $pdf->SetXY($startX, $pdf->GetY());
        $pdf->MultiCell($endX, 6,
            'LOCAÇÃO SPOT DE GUINDASTE ARTICULADO (MUNCK) E FERRAMENTAS DE REMOÇÃO PARA MOVIMENTAÇÃO DE MÁQUINAS' .
            (empty($data['PROJECT_NAME']) ? '' : " | REF: " . $data['IDENTIFIER']),
            0, 'L');

        // Tabela de orçamento
        $pdf->Ln(4);
        $pdf->SetFont('dejavusans','B',12,'',true);
        $pdf->SetXY($startX, $pdf->GetY());            // fixa o X na seção
        $pdf->Cell($endX, 8, 'TABELA DE ORÇAMENTO', 0, 1, 'L');

        $pdf->SetFont('dejavusans','',9,'',true);

        $m        = $pdf->getMargins();
        $pageW    = $pdf->getPageWidth();
        $usableW  = $pageW - $m['left'] - $m['right'];

        $cols   = [15, 17, 90, 30, 20, 35];
        $sum    = array_sum($cols);
        $scale  = min(1, $usableW / $sum);
        $w      = array_map(fn($c) => $c * $scale, $cols);

        $tableW = array_sum($w);
        $startX = $m['left'] + ($usableW - $tableW) / 2;

        // --- HEADER ---
        $pdf->SetFont('dejavusans','',10,'',true);
        $pdf->SetLineStyle(['width'=>0.15, 'cap'=>'butt', 'join'=>'miter', 'dash'=>0, 'color'=>[80,80,80]]);

        $pdf->SetX($startX);
        $pdf->Cell($w[0], 10, 'Item',       1, 0, 'C', true);
        $pdf->Cell($w[1], 10, 'Qnt',        1, 0, 'C', true);
        $pdf->Cell($w[2], 10, 'Descrição',  1, 0, 'C', true);
        $pdf->Cell($w[3], 10, 'Valor Hora', 1, 0, 'C', true);
        $pdf->Cell($w[4], 10, 'Horas',      1, 0, 'C', true);
        $pdf->Cell($w[5], 10, 'Valor',      1, 1, 'C', true);

        // --- LINHAS ---
        $pdf->SetFont('dejavusans','',10,'',true);
        $item_count = 1;
        $valor_total_minimo = 0.0;

        // Máquinas
        foreach (($data['MACHINES'] ?? []) as $machine) {
            $qtde       = 1;
            $descricao  = $machine['machine_name'];
            $valorHoraS = $machine['price_per_hour'];
            $horasS     = $machine['minimum_rental_period'];
            $valorMinS  = $machine['MINIMUM_RENTAL_PRICE'];

            // soma do mínimo (horas)
            $valor_total_minimo += (float) str_replace(['.', ','], ['', '.'], $valorMinS);

            $pdf->SetLineStyle(['width'=>0.15, 'color'=>[80,80,80]]);
            $pdf->SetX($startX);
            $pdf->Cell($w[0], 10, $item_count++, 1, 0, 'C');
            $pdf->Cell($w[1], 10, $qtde,         1, 0, 'C');
            $pdf->Cell($w[2], 10, $descricao,    1, 0, 'L');
            $pdf->Cell($w[3], 10, 'R$ '.$valorHoraS, 1, 0, 'C');
            $pdf->Cell($w[4], 10, $horasS,       1, 0, 'C');
            $pdf->Cell($w[5], 10, $valorMinS,    1, 1, 'C');

            $distFmt  = $machine['MACHINE_DISTANCE_PRICE'] ?? '0,00';
            $distValF = (float) str_replace(['.', ','], ['', '.'], $distFmt);

            // você também tem total_distance e distance_amount no array:
            $km       = $machine['total_distance'] ?? 0;
            $precoKm  = $machine['distance_amount'] ?? 0;
            $precoKmFmt = is_numeric($precoKm) ? number_format($precoKm, 2, ',', '.') : $precoKm;

            if ($distValF > 0) {
                $valor_total_minimo += $distValF;

                $pdf->SetX($startX);
                $pdf->Cell($w[0], 10, '', 1, 0, 'C');
                $pdf->Cell($w[1], 10, '', 1, 0, 'C');
                // descrição explicativa da distância
                $descDist = "Mobilização e desmobilização";
                $pdf->Cell($w[2], 10, $descDist, 1, 0, 'L');
                $pdf->Cell($w[3], 10, '-', 1, 0, 'C');
                $pdf->Cell($w[4], 10, '-', 1, 0, 'C');
                $pdf->Cell($w[5], 10, $distFmt, 1, 1, 'C');
            }
}


        // Operadores
        if (!empty($data['AUX_OPERATORS'])) {
            foreach ($data['AUX_OPERATORS'] as $op) {
                $valor_total_minimo += (float) $op['TOTAL_FLOAT'];

                $pdf->SetLineStyle(['width'=>0.15, 'color'=>[80,80,80]]);
                $pdf->SetX($startX);

                $pdf->Cell($w[0], 10, $item_count++, 1, 0, 'C');
                $pdf->Cell($w[1], 10, $op['QTD'],    1, 0, 'C');
                $pdf->Cell($w[2], 10, $op['ROLE'],   1, 0, 'C');
                $pdf->Cell($w[3], 10, 'R$ '.$op['PRICE'], 1, 0, 'C');
                $pdf->Cell($w[4], 10, $op['HOURS'],  1, 0, 'C');
                $pdf->Cell($w[5], 10, $op['TOTAL'],  1, 1, 'C');
            }
        }

        // Encargos
        $valor_total_encargos = 0.0;
        if (!empty($data['SERVICE_CHARGE'])) {
            foreach ($data['SERVICE_CHARGE'] as $charge) {
                $valor_total_encargos += $this->fromBrl($charge['FEE_AMOUNT']);

                $pdf->SetLineStyle(['width'=>0.15, 'color'=>[80,80,80]]);
                $pdf->SetX($startX);

                $pdf->Cell($w[0], 10, $item_count++,                  1, 0, 'C');
                $pdf->Cell($w[1], 10, 1,                              1, 0, 'C');
                $pdf->Cell($w[2], 10, $charge['SERVICE_CHARGE_NAME'], 1, 0, 'C');
                $pdf->Cell($w[3], 10, '-',                            1, 0, 'C');
                $pdf->Cell($w[4], 10, '-',                            1, 0, 'C');
                $pdf->Cell($w[5], 10, $charge['FEE_AMOUNT'],          1, 1, 'C');
            }
        }

        // Total (mesma largura/centralização)
        $total_geral = $valor_total_minimo + $valor_total_encargos;
        $pdf->SetFont('dejavusans','',10,'',true);
        $pdf->SetFillColor($black[0], $black[1], $black[2]);
        $pdf->SetTextColor(255,255,255);

        $pdf->SetX($startX);
        $pdf->Cell($tableW - $w[5], 12, 'Valor Total', 1, 0, 'R', true);
        $pdf->SetFont('dejavusans','B',10,'',true);
        $pdf->Cell($w[5], 12, 'R$ '.$this->brl($total_geral), 1, 1, 'C', true);

        $pdf->SetTextColor(0,0,0);


        $lineLength = 170;
        $pageW      = $pdf->getPageWidth();
        $secX       = ($pageW - $lineLength) / 2;
        $secW       = $lineLength;

        // CONDIÇÕES GERAIS
        $pdf->Ln(6);

        $pdf->SetFont('dejavusans','B',12,'',true);
        $pdf->SetXY($secX, $pdf->GetY());
        $pdf->Cell($secW, 8, 'CONDIÇÕES GERAIS', 0, 1, 'L');

        $pdf->SetFont('dejavusans','',9,'',true);
        $pdf->SetDrawColor($black[0], $black[1], $black[2]);
        $pdf->SetLineWidth(0.4);

        $bullets = [
            'A CONTRATANTE ficará responsável pela liberação da entrada do veículo e do motorista na área onde será realizado o serviço, sendo providenciada a integração e qualquer outro tipo de liberação que for solicitada;',
            'Os tempos demandados para realização de integração, check list e demais atividades assemelhadas, serão consideradas horas a disposição e cobradas em medição;',
            'Na impossibilidade de execução do serviço com o equipamento acima tratado por qualquer motivo não imputável à contratada, não haverá alterações nos valores cobrados e condições de pagamento descritas nesta proposta de serviços, ficando acordado que a contratante deverá providenciar as licenças e autorizações necessárias para a execução do serviço;',
            'A contagem mínima das horas considerando o valor de horas normais, são entre 7:00h às 18:00h horas de segunda a sexta, trabalhadas ou à disposição',
            'As horas trabalhadas e ou disposição da contratada além do horário diário normal de trabalho (antes das 7h e após às 18h, finais de semana e feriados), serão consideradas Horas Especiais e serão medidas em separado das horas normais e cobradas com adicional de 30%',
            'Período de integração ou treinamento na obra contará como hora trabalhada.',
        ];

        foreach ($bullets as $b) {

            $pdf->SetX($secX);

            $pdf->SetFont('dejavusans','B',8,'',true);
            $pdf->Cell(4, 6, '•', 0, 0, 'L');

            $pdf->SetFont('dejavusans','',8,'',true);
            $pdf->MultiCell($secW - 6, 6, $b, 0, 'L');

            $y = $pdf->GetY();
            $pdf->Line($secX, $y + 1.0, $secX + $secW, $y + 1.0);
            $pdf->Ln(4);
        }

        $pdf->Ln(8);
        $pdf->SetX($secX);
        $pdf->SetFont('dejavusans','B',12,'',true);
        $pdf->Write(0, 'VALIDADE DA PROPOSTA');

        $pdf->SetFont('dejavusans','',10,'',true);
        $pdf->Ln(5);
        $pdf->SetX($secX);
        $pdf->MultiCell($secW, 5, 'A presente proposta tem validade de 30 (Trinta) dias a contar da presente data.', 0, 'L');


        // --- RODAPÉ PRETO PÁGINA INTEIRA ---
        $footerH = 18;
        $pageW   = $pdf->getPageWidth();
        $pageH   = $pdf->getPageHeight();

        $oldAPB     = $pdf->getAutoPageBreak();
        $oldBMargin = $pdf->getBreakMargin();
        $pdf->SetAutoPageBreak(false, 0);

        $yTop = $pageH - $footerH;

        $pdf->SetFillColor(17,17,17);
        $pdf->Rect(0, $yTop, $pageW, $footerH, 'F');

        $pdf->SetDrawColor(255,102,0);
        $pdf->SetLineWidth(0.8);
        $splitX = $pageW * 0.62;
        $pdf->Line($splitX, $yTop + 3, $splitX, $yTop + $footerH - 3);

        $pdf->SetTextColor(255,255,255);

        $pdf->SetFont('dejavusans','B',10,'',true);
        $pdf->SetXY(2, $yTop + 3);
        $pdf->Cell($splitX - 4, 6, 'RODOBRAS GUINDASTES - 01.000.000/0001-01', 0, 0, 'R');

        $pdf->SetFont('dejavusans','',9,'',true);
        $pdf->SetXY($splitX + 2, $yTop + 3);
        $pdf->Cell($pageW - ($splitX + 4), 6, '(48) 99158-2727   ·   (48) 3285-2727', 0, 1, 'L');

        $pdf->SetXY(2, $yTop + 9);
        $pdf->Cell($splitX - 4, 6, 'COMERCIAL@RODOBRASGUINDASTES.COM.BR', 0, 0, 'R');

        $pdf->SetXY($splitX + 2, $yTop + 9);
        $pdf->SetFont('dejavusans','',8,'',true);
        $pdf->Cell($pageW - ($splitX + 4), 6, 'Gerado em: ' . date('d/m/Y H:i'), 0, 1, 'L');

        $pdf->SetTextColor(0,0,0);
        $pdf->SetAutoPageBreak($oldAPB, $oldBMargin);

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
