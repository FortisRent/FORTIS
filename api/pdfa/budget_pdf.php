<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/../db/db.php'; // DBConnection já vem daqui

class BudgetPdfController {
    private $mysqli;

    public function __construct($mysqli){
		$this->mysqli = $mysqli;
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

            // Dados de quem está logado

            // Consulta os dados do cliente
            $budget_list = $this->mysqli->query("SELECT b.uuid AS budget_uuid, u.full_name AS user_name, u.cpf AS user_cpf,
                                                        c.name AS client_name, c.cpf AS cnpj, p.name AS project_name,
                                                        (b.total_fee/100) AS total_fee, (b.amount/100) AS amount, p.identifier, DATE_FORMAT(b.created_at, '%d de %M de %y') AS created_at
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

            // Consulta operadores
            $stmt_employees = $this->mysqli->prepare("  SELECT COUNT(*) AS total_operators, (r.hourly_price / 100) AS hour_price, r.name AS role_name, mf.minimum_rental_period
                                                                                            FROM budget_machine_operator bmo
                                                                                            INNER JOIN `role` r ON bmo.role_id = r.id
                                                                                            INNER JOIN budget_machine bm ON bmo.budget_machine_id = bm.id
                                                                                            INNER JOIN machine m ON bm.machine_id = m.id
                                                                                            INNER JOIN machine_franchise mf ON mf.machine_id = m.id
                                                                                            INNER JOIN budget b ON bm.budget_id = b.id
                                                                                            WHERE b.id = ? AND bmo.deleted_at IS NULL
                                                                                            GROUP BY r.id
                                                                                        ");
            $stmt_employees->bind_param('i', $budget_id);
            $stmt_employees->execute();
            $result_employees = $stmt_employees->get_result();

            $aux_operators_data = [];

            while ($row = $result_employees->fetch_assoc()) {
                $total_operators = $row['total_operators'] ?? 0;
                $hour_price = $row['hour_price'] ?? 0;
                $role_name = $row['role_name'] ?? 'Desconhecido';
                $min_hours = $row['minimum_rental_period'] ?? 0;

                $total_price = $total_operators * $hour_price * $min_hours;

                $aux_operators_data[] = [
                    'QTD' => $total_operators,
                    'ROLE' => $role_name,
                    'PRICE' => number_format($hour_price, 2, ',', '.'),
                    'HOURS' => $min_hours,
                    'TOTAL' => number_format($total_price, 2, ',', '.'),
                    'TOTAL_FLOAT' => $total_price
                ];
            }



    
            $budget_data = $budget_list->fetch_assoc();
            $budget_charges = [];
            while ($charge = $budget_charge_list->fetch_assoc()) {
                $budget_charges[] = [
                    'SERVICE_CHARGE_NAME' => $charge['service_charge_name'],
                    'FEE_AMOUNT' =>  number_format($charge['fee_amount'], 2, ',', '.')
                ];
            }
    
            $this->mysqli->commit();
    
             $data = [
                'IDENTIFIER'             => $budget_data['identifier'] ?? null,
                'CREATED_AT'          => $budget_data['created_at'] ?? null,
                'NAME'             => $budget_data['client_name'] ?? $budget_data['user_name'],
                'DOCUMENT'         => $budget_data['cnpj'] ?? $budget_data['user_cpf'],
                'PROJECT_NAME'     => $budget_data['project_name'] ?? 'Projeto sem nome',
                'MACHINES'         => $machines ?? [],
                'SERVICE_CHARGE'   => $budget_charges ?? [],
                'AUX_OPERATORS'    => $aux_operators_data ?? []
            ];

            return $data;

        } catch (Exception $e) {
            $this->mysqli->rollback();
            throw new Exception("Erro ao processar dados: " . $e->getMessage());
        }
    }
    


    public function export_budget_data_to_pdfa($data) {
        $output_dir = __DIR__ . '/output';
        if (!is_dir($output_dir)) {
            mkdir($output_dir, 0777, true);
        }
    
        $filename = 'proposta_comercial_' . $data['IDENTIFIER'] . '.pdf';
        $output_path = $output_dir . '/' . $filename;
    
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Proposta Comercial_' .$data['IDENTIFIER']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 20, 10, true);
        $pdf->AddPage();
    
        $pdf->Image(dirname(__FILE__) . '/mobile/src/assets/logo-fortis.svg', 10, 8, 50, 0, 'JPG');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(140, 10);
        $pdf->MultiCell(60, 5, "PROPOSTA COMERCIAL – Florianópolis /SC, " . $data['CREATED_AT'], 0, 'R', false);
    
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(140, 8, $data['NAME'], 1, 0, 'L');
        $pdf->Cell(50, 8, $data['DOCUMENT'], 1, 1, 'C');
    
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(0, 8, 'REF: ' .$data['PROJECT_NAME'], 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 6, 'Vimos apresentar nossa proposta de preços para locação guindaste com mão de obra especializada e material de elevação e amarração padrão disponíveis nos equipamentos:', 0, 'L');
    
        $pdf->Ln(3);
    
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(0, 8, 'TABELA RELAÇÃO EQUIPAMENTO/PREÇO/TEMPO:', 0, 'L');

        $pdf->Ln(2);

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(10, 8, 'Item', 1, 0, 'C', 1);
        $pdf->Cell(15, 8, 'Qtade', 1, 0, 'C', 1);
        $pdf->Cell(80, 8, 'Descrição', 1, 0, 'C', 1);
        $pdf->Cell(30, 8, 'Valor Hora Normal', 1, 0, 'C', 1);
        $pdf->Cell(20, 8, 'Horas', 1, 0, 'C', 1);
        $pdf->Cell(35, 8, 'Valor Mínimo', 1, 1, 'C', 1);
    
        $pdf->SetFont('helvetica', '', 9);

        $machines = $data['MACHINES'];
        $item_count = 1;
        $valor_total_minimo = 0;

        // Renderiza máquinas
        foreach ($machines as $machine) {
            $qtde = 1;
            $descricao = $machine['machine_name'];
            $valorHora = $machine['price_per_hour'];
            $horas = $machine['minimum_rental_period'];
            $valorMinimo = $machine['MINIMUM_RENTAL_PRICE'];

            // Convertendo valor mínimo para float
            $valorMinimoFloat = floatval(str_replace(['.', ','], ['', '.'], $valorMinimo));
            $valor_total_minimo += $valorMinimoFloat;

            $pdf->Cell(10, 8, $item_count++ . '.', 1, 0, 'C');
            $pdf->Cell(15, 8, $qtde, 1, 0, 'C');
            $pdf->Cell(80, 8, $descricao, 1, 0, 'C');
            $pdf->Cell(30, 8, 'R$ ' . $valorHora, 1, 0, 'C');
            $pdf->Cell(20, 8, $horas, 1, 0, 'C');
            $pdf->Cell(35, 8, $valorMinimo, 1, 1, 'C');
        }

        // Verifica se há dados de operadores auxiliares
       if (!empty($data['AUX_OPERATORS']) && count($data['AUX_OPERATORS']) > 0) {
            foreach ($data['AUX_OPERATORS'] as $op) {
                $qtd = $op['QTD'];
                $descricao = $op['ROLE'];
                $valorHora = 'R$ ' . $op['PRICE'];
                $horas = $op['HOURS'];
                $valorMinimo = 'R$ ' . $op['TOTAL'];

                // Soma ao total mínimo
                $valor_total_minimo += $op['TOTAL_FLOAT'];

                $pdf->Cell(10, 8, $item_count++, 1, 0, 'C');
                $pdf->Cell(15, 8, $qtd, 1, 0, 'C');
                $pdf->Cell(80, 8, $descricao, 1, 0, 'C');
                $pdf->Cell(30, 8, $valorHora, 1, 0, 'C');
                $pdf->Cell(20, 8, $horas, 1, 0, 'C');
                $pdf->Cell(35, 8, $valorMinimo, 1, 1, 'C');
            }
        }

        // Total final
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(105, 8, 'Valor Mínimo Total', 1, 0, 'C');
        $pdf->Cell(85, 8, 'R$ ' . number_format($valor_total_minimo, 2, ',', '.'), 1, 1, 'C');


        
        //Tabela de taxas
        $pdf->Ln(8);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(190, 8, 'Encargos e Taxas do Projeto', 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(10, 8, '#', 1, 0, 'C');
        $pdf->Cell(140, 8, 'Descrição', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Valor (R$)', 1, 1, 'C');
        
        $charges = $data['SERVICE_CHARGE'];
        
        if(!empty($charges)){

            $pdf->SetFont('helvetica', '', 9);
        
            $item_count = 1;
            $valor_total_encargos = 0;

            foreach ($charges as $charge) {
                $descricao = $charge['SERVICE_CHARGE_NAME'];
                $valor = $charge['FEE_AMOUNT'];

                // Convertendo para float para somar (removendo . e usando , como separador decimal)
                $valor_float = floatval( $valor);
                $valor_total_encargos += $valor_float;

                $pdf->Cell(10, 8, $item_count++, 1, 0, 'C');
                $pdf->Cell(140, 8, $descricao, 1, 0, 'C');
                $pdf->Cell(40, 8, 'R$ ' . $valor, 1, 1, 'C');
            }

            // Total dos encargos
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(150, 8, 'Total dos Encargos e Taxas', 1, 0, 'C');
            $pdf->Cell(40, 8, 'R$ ' . number_format($valor_total_encargos, 2, ',', '.'), 1, 1, 'C');
        }

        // Condições Gerais
        $pdf->Ln(8);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 8, 'CONDIÇÕES GERAIS:', 0, 1, 'L');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(0, 6, '• A CONTRATANTE ficará responsável pela liberação da entrada do veículo e do motorista na área onde será realizado o serviço, sendo providenciada a integração e qualquer outro tipo de liberação que for solicitada;', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 6, '• Os tempos demandados para realização de integração, check list e demais atividades assemelhadas, serão consideradas horas a disposição e cobradas em medição;', 0, 'L');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(0, 6, '• Na impossibilidade de execução do serviço com o equipamento acima tratado por qualquer motivo não imputável à contratada, não haverá alterações nos valores cobrados e condições de pagamento descritas nesta proposta de serviços, ficando acordado que a contratante deverá providenciar as licenças e autorizações necessárias para a execução do serviço;', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 6, '• A contagem mínima das horas considerando o valor de horas normais, são entre 7:00h às 18:00h horas de segunda a sexta, trabalhadas ou à disposição;', 0, 'L');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(0, 6, '•  As horas trabalhadas e ou disposição da contratada além do horário diário normal de trabalho (antes das 7h e após às 18h, finais de semana e feriados), serão consideradas Horas Especiais e serão medidas em separado das horas normais e cobradas com adicional de 30%;', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 6, '• Período de integração ou treinamento na obra contará como hora trabalhada.', 0, 'L');

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, 'VALIDADE DA PROPOSTA – ');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'A presente proposta tem validade de 30 (Trinta) dias a contar da presente data.');


        $pdf->Ln(7);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 6, 'Sendo o que tínhamos para o momento e sempre prontos para atendê-los, subscrevemo-nos.', 0, 'L');

        // Rodapé com data
        $pdf->SetY(265);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(0, 10, 'Gerado em: ' . date('d/m/Y H:i'), 0, 0, 'R');
    
        $pdf->Output($output_path, 'F');
        return '/pdfa/output/' . $filename;
    }
    
    public function generate_pdf($budget_uuid)
    {
        // Chama a função data_pdf para obter os dados
        $data = $this->data_pdf2($budget_uuid);

        // var_dump($data);

        $this->export_budget_data_to_pdfa($data);
		$outputFile = $this->export_budget_data_to_pdfa($data);

		echo json_encode(["output" => $outputFile]);

    }
}
?>
