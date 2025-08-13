<?php
	require_once('./service/utils.php');

    // {
    //     "budget_uuid": "JWm0CXSJD01UojeutL1xNshl9pGXqSujey6nKjQa9DEXT9jMgelWwXPUQ72j84aT",
    //     "status_name": "ACEITO"
    // }

	class BudgetHistoryController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}
		 # "GET /v1/budget/" 
		public function get_all_budgets_histories() {
			$query = "  SELECT bh.uuid AS history_uuid, p.id AS budget_id, p.uuid AS budget_uuid, p.name AS budget_name, s.name AS status_name, bh.details, DATE_FORMAT(bh.created_at, '%d/%m/%Y') AS created_at, DATE_FORMAT(bh.deleted_at, '%d/%m/%Y') AS deleted_at
                                FROM budget_history bh
                                INNER JOIN budget p ON bh.budget_id = p.id
                                INNER JOIN status s ON bh.status_id = s.id;";

			$result = $this->mysqli->query($query);

			if ($result) {
				$budget = [];
				while ($row = $result->fetch_assoc()) {
					$budget[] = $row;
				}
				echo json_encode(["budget" => $budget]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        # "GET /v1/budget/(\w+)"       
		public function get_history_by_uuid($budget_uuid) {
			$token = validate_token();

			$query = " SELECT bh.uuid AS history_uuid, p.id AS budget_id, p.uuid AS budget_uuid, p.name AS budget_name, s.name AS status_name, bh.details, DATE_FORMAT(bh.created_at, '%d/%m/%Y') AS created_at, DATE_FORMAT(bh.deleted_at, '%d/%m/%Y') AS deleted_at
                                FROM budget_history bh
                                INNER JOIN budget p ON bh.budget_id = p.id
                                INNER JOIN status s ON bh.status_id = s.id
                                WHERE bh.uuid = ?;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$budget_history = [];
			while ($row = $result->fetch_assoc()) {
				$budget_history[] = $row;
			}

			echo json_encode(["budget_history" => $budget_history]);
		}

        public function get_budget_history_by_budget_uuid($budget_uuid) {
			$token = validate_token();

			$query = "  SELECT bh.uuid AS history_uuid, p.id AS budget_id, p.uuid AS budget_uuid, p.name AS budget_name, s.name AS status_name, bh.details, DATE_FORMAT(bh.created_at, '%d/%m/%Y') AS created_at, DATE_FORMAT(bh.deleted_at, '%d/%m/%Y') AS deleted_at
                                FROM budget_history bh
                                INNER JOIN budget p ON bh.budget_id = p.id
                                INNER JOIN status s ON bh.status_id = s.id
                                WHERE bh.budget_id = (SELECT id FROM budget WHERE uuid = ?);";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$budget_history = [];
			while ($row = $result->fetch_assoc()) {
				$budget_history[] = $row;
			}

			echo json_encode(["budget_history" => $budget_history]);
		}

        // "POST /v1/budget/history/"
		public function create_budget_history() {
			$token = validate_token();

			$data = validate_payload([ "budget_uuid", "status_name"]);


			$query = "INSERT INTO budget_history (budget_id, status_id)
						VALUES ((SELECT id FROM budget WHERE uuid = ?), (SELECT id FROM status WHERE name = ?))";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ss', $data->budget_uuid, $data->status_name);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Histórico adicionado ao orçamento."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao adicionar histórico para um orçamento."]);
			}
		}

        // "PUT /v1/budget/history/(\w+)"
		public function update_budget_history($budget_history_uuid) {

			validate_token();

			$data = validate_payload( ["details"]);

			$query = "UPDATE budget_history SET details = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ss', $data->details, $budget_history_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Histórico do orçamento atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar histórico do orçamento."]);
			}
		}
        
        // DELETE /v1/budget/([\w-]+)
        public function delete_budget_history($budget_history_uuid) {
			$query = "UPDATE budget_history SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_history_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Histórico do orçamento desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar orçamento."]);
			}
		}

          // "PUT /v1/budget/history/reactivate/(\w+)"
		public function reactivate_budget_history($budget_history_uuid) {
			$query = "UPDATE budget_history SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_history_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Histórico do orçamento reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar histórico do orçamento."]);
			}
		}

	}