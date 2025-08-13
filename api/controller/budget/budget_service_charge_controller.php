<?php
	require_once('./service/utils.php');


	class BudgetServiceChargeController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		public function get_budget_service_charge_by_uuid($budget_service_charge_uuid) {
			$query = "  SELECT bsc.uuid AS budget_service_charge_uuid, sc.uuid AS service_charge_uuid, sc.name as service_charge_name, b.uuid AS budget_uuid, DATE_FORMAT(bsc.created_at, '%d/%m/%Y') as created_at
								FROM budget_service_charge bsc
								INNER JOIN budget b ON bsc.budget_id = b.id
								INNER JOIN service_charge sc ON bsc.service_charge_id = sc.id
                                WHERE bsc.uuid = ? AND b.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_service_charge_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "budget not found"]);
				exit();
			}

			$budget_service_charge = $result->fetch_assoc();
			echo json_encode(["budget_service_charge" => $budget_service_charge]);
		}

        // "POST /v1/budget/service/charge/"
		public function create_budget_service_charge() {
			validate_token();
			$data = validate_payload(["condition", "amount", "budget_uuid","service_charge_list"]);

			$this->mysqli->begin_transaction();
		
			try {
				$query = "INSERT INTO budget_service_charge (budget_id, service_charge_id)
								 VALUES ((SELECT id FROM budget WHERE uuid = ?), (SELECT id FROM service_charge WHERE uuid = ?))";
				$stmt = $this->mysqli->prepare($query);

                foreach ($data->service_charge_list as $service_charge) {
                    $stmt->bind_param('ss', $data->budget_uuid, $service_charge->service_charge_uuid);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Erro ao relacionar taxas ao orçamento.");
                    }
                }

				$query2 = "UPDATE budget SET observation = ?, `condition` = ?, amount = ? WHERE uuid = ?";

				$stmt2 = $this->mysqli->prepare($query2);
				$stmt2->bind_param('ssis', $data->observation, $data->condition, $data->amount, $data->budget_uuid);
                    
				if (!$stmt2->execute()) {
					throw new Exception("Erro ao adicionar tipos taxas ao orçamento.");
				}

				$query_history = "INSERT INTO budget_history (budget_id, status_id, details)
				VALUES ((SELECT id FROM budget WHERE uuid = ?), 3, ?)";

				$stmt_history = $this->mysqli->prepare($query_history);
				$stmt_history->bind_param('ss', $data->budget_uuid, $data->details);

				if (!$stmt_history->execute()) {
				throw new Exception("Erro ao registrar histórico do orçamento.");
				}
		
				$this->mysqli->commit();
				
				http_response_code(201);
				echo json_encode(["message" => "Valores, observações e histórico adicionados ao orçamento."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}

		// "PUT /v1/budget/service/charge/([\w-]+)"
		public function update_budget_service_charge($budget_service_charge_uuid) {

			validate_token();
			$data = validate_payload( ["service_charge_uuid"]);

			$query = "UPDATE budget_service_charge SET service_charge_id = (SELECT id FROM service_charge WHERE uuid = ?) WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ss', $data->service_charge_uuid, $budget_service_charge_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Taxa associada ao orçamento atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar associação de taxa com o orcąmento."]);
			}
		}

        // "DELETE /v1/budget/service/charge/(\w+)"
		public function delete_budget_service_charge($budget_service_charge_uuid) {
			validate_token();
			
			$query = "UPDATE budget_service_charge SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_service_charge_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Taxa desconectada do orçamento."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desconectar taxa do orçamento."]);
			}
		}
	}