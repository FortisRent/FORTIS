<?php
	require_once('./service/utils.php');


	class BudgetPaymentController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		// "GET /v1/budget/payment/([\w-]+)" 	
		public function get_budget_payment_by_uuid($budget_payment_uuid) {
			validate_token();

			$query = " SELECT bp.uuid AS budget_payment_uuid, (bp.extra / 100) AS extra, (bp.discount / 100) AS discount, bp.extra_description, bp.discount_description, b.uuid AS budget_uuid, p.payer_name, p.cnpj
								FROM budget_payment bp
								INNER JOIN budget b ON bp.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
								WHERE bp.uuid = ? AND bp.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_payment_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Budget Payment not found"]);
				exit();
			}

			$budget_payment = $result->fetch_assoc();
			echo json_encode(["budget_payment" => $budget_payment]);
		}

		// "GET /v1/budget/payment/budget/([\w-]+)" 
		public function get_budget_payment_by_budget_uuid($budget_uuid) {

           validate_token();

			$query = " SELECT bp.uuid AS budget_payment_uuid, (bp.extra / 100) AS extra, (bp.discount / 100) AS discount, bp.extra_description, bp.discount_description, b.uuid AS budget_uuid, p.payer_name, p.cnpj
								FROM budget_payment bp
								INNER JOIN budget b ON bp.budget_id = b.id
								WHERE b.uuid = ? AND bp.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$budget_payment = [];
			while ($row = $result->fetch_assoc()) {
				$row['extra'] = number_format($row['extra'], 2, ',', '.');
				$row['discount'] = number_format($row['discount'], 2, ',', '.');
				$budget_payment[] = $row;
			}

			echo json_encode(["budget_payment" => $budget_payment]);
		}

        // "POST /v1/budget/payment/"
		public function create_budget_payment() {
			validate_token();

			$data = validate_payload(["budget_uuid", "budget_machine_list", "operator_list", "discount", "extra", "extra_description", "discount_description"]);
		
			$this->mysqli->begin_transaction();
		
			try {
				$payment_query = "INSERT INTO budget_payment (budget_id, total_amount, discount, extra, extra_description, discount_description)
								 VALUES ((SELECT id FROM budget WHERE uuid = ?), ?, ?, ?, ?, ?)";
				$payment_stmt = $this->mysqli->prepare($payment_query);
				if (!$payment_stmt) {
					throw new Exception("Erro na preparação do statement de pagamento: " . $this->mysqli->error);
				}
				$payment_stmt->bind_param('siiiss', $data->budget_uuid, $data->total_amount, $data->discount, $data->extra, $data->extra_description, $data->discount_description);
				if (!$payment_stmt->execute()) {
					throw new Exception("Erro ao adicionar pagamento do orçamento: " . $payment_stmt->error);
				}
				$payment_stmt->close();
		
				if (!empty($data->budget_machine_list) && is_array($data->budget_machine_list)) {

					$bm_query = "UPDATE budget_machine SET machine_start = ?, machine_end = ?, machine_break_start = ?, machine_break_end = ? WHERE uuid = ?";
					$bm_stmt = $this->mysqli->prepare($bm_query);

					if (!$bm_stmt) {
						throw new Exception("Erro na preparação do statement de budget_machine: " . $this->mysqli->error);
					}
		
					foreach ($data->budget_machine_list as $bm) {
						$bm_stmt->bind_param('sssss', $bm->machine_start, $bm->machine_end, $bm->machine_break_start, $bm->machine_break_end, $bm->budget_machine_uuid);
		
						if (!$bm_stmt->execute()) {
							throw new Exception("Erro ao atualizar budget_machine (UUID: {$bm->budget_machine_uuid}): " . $bm_stmt->error);
						}
					}
					$bm_stmt->close();
				}

				if (!empty($data->operator_list) && is_array($data->operator_list)) {

					$op_query = "UPDATE budget_machine_operator SET operator_start = ?, operator_end = ?, operator_break_start = ?, operator_break_end = ? WHERE uuid = ?";
					$op_stmt = $this->mysqli->prepare($op_query);

					if (!$op_stmt) {
						throw new Exception("Erro na preparação do statement de budget_machine_operator: " . $this->mysqli->error);
					}
		
					foreach ($data->operator_list as $op) {
						$op_stmt->bind_param('sssss', $op->operator_start, $op->operator_end, $op->operator_break_start, $op->operator_break_end, $op->budget_machine_operator_uuid);
		
						if (!$op_stmt->execute()) {
							throw new Exception("Erro ao atualizar budget_machine_operator (UUID: {$op->budget_machine_operator_uuid}): " . $op_stmt->error);
						}
					}
					$op_stmt->close();
				}

				$update_distance_query = "UPDATE budget SET total_distance = ? WHERE uuid = ?";
				$update_distance_stmt = $this->mysqli->prepare($update_distance_query);

				if (!$update_distance_stmt) {
					throw new Exception("Erro na preparação do statement de atualização de distância: " . $this->mysqli->error);
				}
				$update_distance_stmt->bind_param('ds', $data->total_distance, $data->budget_uuid);

				if (!$update_distance_stmt->execute()) {
					throw new Exception("Erro ao atualizar total_distance: " . $update_distance_stmt->error);
				}
				$update_distance_stmt->close();

				$query_history = "INSERT INTO budget_history (budget_id, status_id, details)
								  VALUES ((SELECT id FROM budget WHERE uuid = ?), 8, ?)";
				$stmt_history = $this->mysqli->prepare($query_history);
				$stmt_history->bind_param('ss', $data->budget_uuid, $data->details);
		
				if (!$stmt_history->execute()) {
					throw new Exception("Erro ao registrar histórico do orçamento.");
				}

				$this->mysqli->commit();
		
				http_response_code(201);
				echo json_encode(["message" => "Pagamento do orçamento adicionado e atualizações realizadas com sucesso."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}		
		
		// "PUT /v1/budget/payment/([\w-]+)"
		public function update_budget_payment($budget_payment_uuid) {
			validate_token();

			$data = validate_payload([]);

			$query = "UPDATE budget_payment SET discount = ?, extra = ?, discount_description = ?, extra_description = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('iisss', $data->discount, $data->extra, $data->discount_description, $data->extra_description, $budget_payment_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Pagamento do orçamento atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar pagamento do orçamento."]);
			}
		}

		// "DELETE /v1/budget/payment/([\w-]+)"
		public function delete_budget_payment($budget_payment_uuid) {
			$query = "UPDATE budget_payment SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_payment_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Pagamento do orçamento desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar pagamento do orçamento."]);
			}
		}
		
	}