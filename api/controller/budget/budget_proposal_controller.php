<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class BudgetProposalController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/budget/proposal/(\w+)"
		public function get_budget_proposal_by_uuid($budget_proposal_uuid) {
			$query = "  SELECT bp.*, client.full_name as client_name, m.name as machine_name, c.name as company_name, s.name as status_name, b.uuid as budget_uuid
								FROM budget_proposal bp
                                INNER JOIN budget b ON bp.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN budget_machine_proposal bmp ON bmp.budget_proposal_id = bp.id
								INNER JOIN budget_machine bm ON bmp.budget_machine_id = bm.id
                                INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN `user` as client ON p.user_id = client.id
                                INNER JOIN company c ON m.company_id = c.id
								INNER JOIN budget_history bh ON bh.budget_id = b.id
								INNER JOIN status s ON bh.status_id = s.id
                                WHERE bp.uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_proposal_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Proposta não encontrada."]);
				exit();
			}

			$budget_proposal = $result->fetch_assoc();
			echo json_encode(["budget_proposal" => $budget_proposal]);
		}

		public function get_proposal_details_by_budget_proposal_uuid($budget_proposal_uuid) {

            validate_token();

			$query_budget_company = " SELECT c.name, c.cnpj
															FROM budget_proposal bp
															INNER JOIN budget_machine_proposal bmp ON bmp.budget_proposal_id = bp.id
															INNER JOIN budget_machine bm ON bmp.budget_machine_id = bm.id
															INNER JOIN machine m ON bm.machine_id = m.id
															INNER JOIN company c ON m.company_id = c.id
															WHERE bp.id = (SELECT id FROM budget_proposal WHERE uuid = ?) 
															AND bp.deleted_at IS NULL;";
		
			$stmt_budget_company = $this->mysqli->prepare($query_budget_company);
			$stmt_budget_company->bind_param('s', $budget_proposal_uuid);
			$stmt_budget_company->execute();
			$result_budget_company = $stmt_budget_company->get_result();
			$budget_company_data = $result_budget_company->fetch_assoc();

			$query_project_and_proposal = " SELECT b.uuid as budget_uuid, p.uuid as project_uuid, p.name as project_name, p.description as project_description, DATE_FORMAT(bp.expected_date, '%d/%m/%Y') as expected_date, (bp.amount/100) AS amount, 
																					bp.condition, bp.observation, mc.name AS name_machine_category, pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, city.name AS city_name, gs.name AS state_name
																	FROM budget_proposal bp
																	INNER JOIN budget_machine_proposal bmp ON bmp.budget_proposal_id = bp.id
																	INNER JOIN budget_machine bm ON bmp.budget_machine_id = bm.id
																	INNER JOIN budget b ON bm.budget_id = b.id
																	INNER JOIN project p ON b.project_id = p.id
																	INNER JOIN project_address pa ON pa.project_id = p.id
																	INNER JOIN city city ON pa.city_id = city.id
																	INNER JOIN geo_state gs ON city.state_id = gs.id
																	LEFT JOIN project_stage ps ON ps.project_id = p.id
																	INNER JOIN machine_category mc ON ps.machine_category_id = mc.id
																	INNER JOIN machine m ON bm.machine_id = m.id
																	INNER JOIN company c ON m.company_id = c.id
																	WHERE bp.id = (SELECT id FROM budget_proposal WHERE uuid = ?) 
																	AND bp.deleted_at IS NULL;";
		
			$stmt_project_and_proposal = $this->mysqli->prepare($query_project_and_proposal);
			$stmt_project_and_proposal->bind_param('s', $budget_proposal_uuid);
			$stmt_project_and_proposal->execute();
			$result_project_and_proposal = $stmt_project_and_proposal->get_result();
			$project_and_proposal_data = $result_project_and_proposal->fetch_assoc();

			$project_and_proposal_data['amount'] = number_format($project_and_proposal_data['amount'], 2, ',', '.');



			$query_budget_charge = "	SELECT b.uuid as budget_uuid, sc.name AS charge_name, (bsc.amount / 100) AS fee_amount, sc.observation
															FROM budget_service_charge bsc
															INNER JOIN budget b ON bsc.budget_id = b.id
															INNER JOIN service_charge sc ON bsc.service_charge_id = sc.id
															INNER JOIN budget_machine bm ON bm.budget_id = b.id
															INNER JOIN budget_machine_proposal bmp ON bmp.budget_machine_id = bm.id
															INNER JOIN budget_proposal bp ON bmp.budget_proposal_id = bp.id
															WHERE bp.id = ( SELECT id FROM budget_proposal WHERE uuid = ?) AND bp.deleted_at IS NULL;";
		
			$stmt_budget_charge = $this->mysqli->prepare($query_budget_charge);
			$stmt_budget_charge->bind_param('s', $budget_proposal_uuid);
			$stmt_budget_charge->execute();
			$result_budget_charge = $stmt_budget_charge->get_result();

			$budget_charge_data = [];

			while ($row = $result_budget_charge->fetch_assoc()) {
				$row['fee_amount'] = number_format($row['fee_amount'], 2, ',', '.');
				$budget_charge_data[] = $row;
			}
		
			$query_budget_machine = "SELECT bp.uuid AS budget_proposal_uuid, bm.id AS budget_machine_id, bm.uuid AS budget_machine_uuid, b.uuid AS budget_uuid, m.name AS machine_name, c.name AS company_name, mc.name AS category_name, m.parameters, m.max_volume, m.max_weight, m.max_height, m.max_radius, 
																			m.year_manufacture, m.brand, m.license_plate, m.serial_number, m.jib, m.chassis_number, m.crane_truck_data, m.trailer_data, bp.observation AS proposal_observation, (bp.amount/100) AS amount, (bp.total_fee/100) AS total_fee, DATE_FORMAT(bp.expected_date, '%d/%m/%Y') AS expected_date, bp.condition, 
																			(m.price/100) AS price, (mf.price_per_hour/100) AS price_per_hour, mf.minimum_rental_period, (mf.price_per_distance/100) AS price_per_distance, mf.distance_amount, mf.special_hour_fee, mf.observation,
																			DATE_FORMAT(b.created_at, '%d/%m/%Y') AS buget_created_at
															FROM budget_machine bm
															INNER JOIN budget b ON bm.budget_id = b.id
															LEFT JOIN budget_machine_proposal bmp ON bmp.budget_machine_id = bm.id
                                                            LEFT JOIN budget_proposal bp ON bmp.budget_proposal_id = bp.id
															INNER JOIN machine m ON bm.machine_id = m.id
															INNER JOIN machine_category mc ON m.category_id = mc.id
                                                            LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
															INNER JOIN company c ON m.company_id = c.id
															WHERE bp.id = (SELECT id FROM budget_proposal WHERE uuid = ?) AND bp.deleted_at IS NULL AND bm.deleted_at IS NULL";
		
			$stmt_budget_machine = $this->mysqli->prepare($query_budget_machine);
			$stmt_budget_machine->bind_param('s', $budget_proposal_uuid);
			$stmt_budget_machine->execute();
			$result_budget_machine = $stmt_budget_machine->get_result();
		
			$proposals = [];

			while ($row = $result_budget_machine->fetch_assoc()) {
				$row['parameters'] = json_decode($row['parameters'], true);
				$row['crane_truck_data'] = json_decode($row['crane_truck_data'], true);
				$row['trailer_data'] = json_decode($row['trailer_data'], true);
				$row['employees'] = [];

				$minimum_rental_price = $row['price_per_hour'] * 10;
				$minimum_rental_price = number_format($minimum_rental_price, 2, ',', '.');

				// Buscar operadores vinculados à máquina
				$query_employees = "SELECT e.uuid, op.full_name AS operator_name
									FROM budget_machine_operator bmo
									INNER JOIN employee e ON bmo.employee_id = e.id
									INNER JOIN user op ON e.user_id = op.id
									WHERE bmo.budget_machine_id = ?";
				
				$stmt_employees = $this->mysqli->prepare($query_employees);
				$stmt_employees->bind_param('i', $row['budget_machine_id']);
				$stmt_employees->execute();
				$result_employees = $stmt_employees->get_result();

				while ($emp = $result_employees->fetch_assoc()) {
					$row['employees'][] = $emp;
				}

				$proposal_id = $row['proposal_observation'] . '-' . $row['amount'] . '-' . $row['expected_date']; // identificador único (ou use ID real se preferir)

				if (!isset($proposals[$proposal_id])) {
					$proposals[$proposal_id] = [
						'budget_proposal_uuid' => $row['budget_proposal_uuid'],
						'proposal_observation' => $row['proposal_observation'],
						'amount' => number_format($row['amount'], 2, ',', '.'),
						'total_fee' => number_format($row['total_fee'], 2, ',', '.'),
						'expected_date' => $row['expected_date'],
						'condition' => $row['condition'],
						'machines' => []
					];
				}

				$proposals[$proposal_id]['machines'][] = [
					'budget_machine_uuid'   => $row['budget_machine_uuid'],
					'machine_name'          => $row['machine_name'],
					'company_name'          => $row['company_name'],
					'category_name'         => $row['category_name'],
					'max_volume'			=> $row['max_volume'],
					'max_weight'			=> $row['max_weight'],
					'max_height'			=> $row['max_height'],
					'max_radius'			=> $row['max_radius'],
					'parameters'            => $row['parameters'],
					'crane_truck_data'            => $row['crane_truck_data'],
					'trailer_data'            => $row['trailer_data'],
					'year_manufacture'      => $row['year_manufacture'],
					'brand'                 => $row['brand'],
					'license_plate'         => $row['license_plate'],
					'serial_number'         => $row['serial_number'],
					'chassis_number'         => $row['chassis_number'],
					'jib'         						=> $row['jib'],
					'price'                 => number_format($row['price'], 2, ',', '.'),
					'price_per_hour'        => number_format($row['price_per_hour'], 2, ',', '.'),
					'minimum_rental_period' => $row['minimum_rental_period'],
					'price_per_distance'    => number_format($row['price_per_distance'], 2, ',', '.'),
					'distance_amount'       => number_format($row['distance_amount'], 2, ',', '.'),
					'special_hour_fee'      => $row['special_hour_fee'],
					'observation'           => $row['observation'],
					'employees'             => $row['employees'],
				];
			}
		
			echo json_encode([
				'budget_company_data' => $budget_company_data,
				"budget" => $project_and_proposal_data,
				"budget_machine" =>array_values($proposals),
				"budget_service_charge" =>$budget_charge_data,
				"minimum_rental_price" => $minimum_rental_price,
			]);
		}


        //  "GET /v1/budget/proposal/budget/(\w+)
		public function get_budget_proposal_by_budget_uuid($budget_uuid) {

			$query = "  SELECT 	bp.uuid AS budget_proposal_uuid, bp.observation, (bp.amount / 100) AS amount, bp.condition, DATE_FORMAT(b.created_at, '%d/%m/%Y') AS created_at, 
													b.uuid AS budget_uuid, GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ' / ') AS machine_name,c.name AS company_name,s.name AS status_name
									FROM budget_proposal bp
									INNER JOIN budget_machine_proposal bmp ON bmp.budget_proposal_id = bp.id
									INNER JOIN budget_machine bm ON bmp.budget_machine_id = bm.id
									INNER JOIN budget b ON bm.budget_id = b.id
									INNER JOIN project p ON b.project_id = p.id
									INNER JOIN machine m ON bm.machine_id = m.id
									INNER JOIN company c ON m.company_id = c.id
									INNER JOIN budget_history bh ON bh.id = (
										SELECT bh1.id 
										FROM budget_history bh1 
										WHERE bh1.budget_id = b.id 
										ORDER BY bh1.created_at DESC 
										LIMIT 1
									)
									INNER JOIN status s ON bh.status_id = s.id
									WHERE bp.budget_id = (SELECT id FROM budget WHERE uuid = ?)
									AND bp.deleted_at IS NULL 
									AND bmp.deleted_at IS NULL
									GROUP BY 
										bp.id, bp.uuid, bp.observation, bp.amount, 
										bp.condition, b.created_at, b.uuid, c.id, c.name, s.name;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$budget_proposals = [];
			while ($row = $result->fetch_assoc()) {
				$budget_proposals[] = $row;
			}

			echo json_encode(["budget_proposals" => $budget_proposals]);
		}

		// "GET /v1/budget/proposal/project/([\w-]+)" 
		public function get_budget_proposal_by_project_uuid($project_uuid){
			$query = "		SELECT 	bp.uuid AS budget_proposal_uuid, bp.observation, (bp.amount / 100) AS amount, bp.condition, DATE_FORMAT(b.created_at, '%d/%m/%Y') AS created_at, 
													b.uuid AS budget_uuid, GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ' / ') AS machine_name,c.name AS company_name, c.uuid AS company_uuid, s.name AS status_name
									FROM budget_proposal bp
									INNER JOIN budget_machine_proposal bmp ON bmp.budget_proposal_id = bp.id
									INNER JOIN budget_machine bm ON bmp.budget_machine_id = bm.id
									INNER JOIN budget b ON bm.budget_id = b.id
									INNER JOIN project p ON b.project_id = p.id
									INNER JOIN machine m ON bm.machine_id = m.id
									INNER JOIN company c ON m.company_id = c.id
									INNER JOIN budget_history bh ON bh.id = (
										SELECT bh1.id 
										FROM budget_history bh1 
										WHERE bh1.budget_id = b.id 
										ORDER BY bh1.created_at DESC 
										LIMIT 1
									)
									INNER JOIN status s ON bh.status_id = s.id
									WHERE p.id = (SELECT id FROM project WHERE uuid = ?)
									AND bp.deleted_at IS NULL 
									AND bmp.deleted_at IS NULL
									GROUP BY 
										bp.id, bp.uuid, bp.observation, bp.amount, 
										bp.condition, b.created_at, b.uuid, c.id, c.name, s.name;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$budget_proposals = [];
			while ($row = $result->fetch_assoc()) {
				$row['amount'] = number_format($row['amount'], 2, ',', '.');
				$budget_proposals[] = $row;
			}

			echo json_encode(["budget_proposals" => $budget_proposals]);
		}

        // "POST /v1/budget/proposal/"
		public function create_budget_proposal() {
			validate_token();
			$data = validate_payload(["condition", "amount", "budget_uuid", "budget_machine_uuid_list", "service_charge_list"]);
		
			$this->mysqli->begin_transaction();
		
			try {
				// Insert da proposta
				$query_budget_proposal = "INSERT INTO budget_proposal (observation, amount, total_fee, expected_date, `condition`) VALUES (?, ?, ?, ?, ?)";
				
				$stmt_budget_proposal = $this->mysqli->prepare($query_budget_proposal);
				$stmt_budget_proposal->bind_param('siiss', $data->observation, $data->amount, $data->total_fee, $data->expected_date, $data->condition);
		
				if (!$stmt_budget_proposal->execute()) {
					throw new Exception("Erro ao cadastrar proposta do orçamento.");
				}

				// Último id da proposta que foi criada
				$budget_proposal_id = $this->mysqli->insert_id;

				$query_conexion = "INSERT INTO budget_machine_proposal (budget_machine_id, budget_proposal_id)
							VALUES ((SELECT id FROM budget_machine WHERE uuid = ?), ?)";

				$stmt_conexion = $this->mysqli->prepare($query_conexion);

				foreach ($data->budget_machine_uuid_list as $uuid) {
					$stmt_conexion->bind_param("si", $uuid, $budget_proposal_id);
					if (!$stmt_conexion->execute()) {
						throw new Exception("Erro ao vincular máquinas à proposta.");
					}
				}

				// Insert das taxas
				$query = "INSERT INTO budget_service_charge (budget_id, service_charge_id)
								 VALUES ((SELECT id FROM budget WHERE uuid = ?), (SELECT id FROM service_charge WHERE uuid = ?))";
				$stmt = $this->mysqli->prepare($query);

                foreach ($data->service_charge_list as $service_charge) {
                    $stmt->bind_param('ss', $data->budget_uuid, $service_charge->service_charge_uuid);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Erro ao relacionar taxas ao orçamento.");
                    }
                }
		
				// Insert do histórico
				$query_history = "INSERT INTO budget_history (budget_id, status_id, details)
								  VALUES ((SELECT id FROM budget WHERE uuid = ?), 3, ?)";
				
				$stmt_history = $this->mysqli->prepare($query_history);
				$stmt_history->bind_param('ss', $data->budget_uuid, $data->details);
				
				if (!$stmt_history->execute()) {
					throw new Exception("Erro ao atualizar histórico do orçamento.");
				}
		
				$this->mysqli->commit();
				
				http_response_code(201);
				echo json_encode(["message" => "Proposta e taxas cadastradas, histórico atualizado com sucesso."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
		
        // "PUT /v1/budget/proposal/(\w+)"
		public function update_budget_proposal($budget_proposal_uuid) {

			validate_token();

			$data = validate_payload( ["description", "amount", "expect_date"]);

			$query = "UPDATE budget_proposal SET description = ?, amount = ?, expect_date = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('siss', $data->description, $data->amount, $data->expect_date, $budget_proposal_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Proposta atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar proposta."]);
			}
		}

        // "DELETE /v1/budget/proposal/(\w+)"
		public function cancel_budget_proposal($budget_proposal_uuid) {
			validate_token();

			try {
				$this->mysqli->begin_transaction();
		
				$query = "UPDATE budget_proposal SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('s', $budget_proposal_uuid);	
				
				if (!$stmt->execute()) {
					throw new Exception("Erro ao cancelar proposta.");
				}
		
				$query2 = "INSERT INTO budget_history (budget_id, status_id)
						  VALUES ((SELECT bm.budget_id 
											FROM budget_proposal bp 
											INNER JOIN budget_machine_proposal bmp ON bmp.budget_proposal_id = bp.id 
											INNER JOIN budget_machine bm ON bmp.budget_machine_id = bm.id
											WHERE bp.uuid = ?), 16)";
				
				$stmt2 = $this->mysqli->prepare($query2);
				$stmt2->bind_param('s', $budget_proposal_uuid);
				
				if (!$stmt2->execute()) {
					throw new Exception("Erro ao adicionar status de recusado a um orçamento.");
				}
				
				$this->mysqli->commit();
				http_response_code(200);
				echo json_encode(["message" => "Proposta cancelada e histórico registrado com sucesso."]);

			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}

		public function accept_budget_proposal($budget_proposal_uuid) {
			validate_token();

			$data = validate_payload(['expected_date']);

			try {
				$this->mysqli->begin_transaction();
		
				$query = "UPDATE budget_proposal bp SET bp.expected_date = ? WHERE bp.uuid = ?";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('ss', $data->expected_date, $budget_proposal_uuid);
				
				if (!$stmt->execute()) {
					throw new Exception("Erro ao aceitar proposta.");
				}
		
				$query2 = "INSERT INTO budget_history (budget_id, status_id)
						  VALUES ((SELECT bm.budget_id 
						  					FROM budget_proposal bp 
											INNER JOIN budget_machine_proposal bmp ON bmp.budget_proposal_id = bp.id 
											INNER JOIN budget_machine bm ON bmp.budget_machine_id = bm.id
											WHERE bp.uuid = ?), 1)";
				
				$stmt2 = $this->mysqli->prepare($query2);
				$stmt2->bind_param('s', $budget_proposal_uuid);
				
				if (!$stmt2->execute()) {
					throw new Exception("Erro ao adicionar status de aceito a um orçamento.");
				}
				
				$this->mysqli->commit();
				http_response_code(200);
				echo json_encode(["message" => "Proposta aceita e histórico registrado com sucesso."]);

			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
	}