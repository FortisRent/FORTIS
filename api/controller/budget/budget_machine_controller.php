<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class BudgetMachineController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/budget/(\w+)"
		public function get_budget_machine_by_uuid($budget_uuid) {
			$query = "  SELECT bm.uuid as budget_machine_uuid, p.uuid as project_uuid, p.name as project_name, m.name as machine_name, c.name as company_name, DATE_FORMAT(b.created_at, '%d/%m/%Y') as created_at
								FROM budget_machine bm
								LEFT JOIN budget b ON bm.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN user as client ON p.user_id = client.id
								INNER JOIN machine m ON bm.machine_id = m.id
                                INNER JOIN company c ON m.company_id = c.id
                                WHERE bm.uuid = ? AND bm.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "budget not found"]);
				exit();
			}

			$budget_machine = $result->fetch_assoc();
			echo json_encode(["budget_machine" => $budget_machine]);
		}

        //  "GET /v1/budget/project/(\w+)
		public function get_budget_machine_by_project_uuid($project_uuid) {

            validate_token();

			$query = "  SELECT bm.uuid as budget_machine_uuid, p.uuid as project_uuid, p.name as project_name, m.name as machine_name, c.name as company_name, DATE_FORMAT(b.created_at, '%d/%m/%Y') as created_at
								FROM budget_machine bm
								LEFT JOIN budget b ON bm.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN user as client ON p.user_id = client.id
								INNER JOIN machine m ON bm.machine_id = m.id
                                INNER JOIN company c ON m.company_id = c.id
								WHERE p.uuid = ? AND bm.deleted_at IS NULL";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$budget_machine = [];
			while ($row = $result->fetch_assoc()) {
				$budget_machine[] = $row;
			}

			echo json_encode(["budget_machine" => $budget_machine]);
		}

        // // "POST /v1/budget/"
		public function create_budget_machine() {
			$token = validate_token();
			$data = validate_payload(["budget_uuid", "machine_uuid"]);

			$this->mysqli->begin_transaction();
		
			try {
				$query_budget = "INSERT INTO budget_machine (machine_id, budget_id)
								 VALUES ((SELECT id FROM machine WHERE uuid = ?), (SELECT id FROM budget WHERE uuid = ?))";
		
				$stmt_budget = $this->mysqli->prepare($query_budget);
				$stmt_budget->bind_param('ss', $data->machine_uuid, $data->budget_uuid);
		
				if (!$stmt_budget->execute()) {
					throw new Exception("Erro ao adicionar máquina em um orçamento.");
				}
		
				$this->mysqli->commit();
				
				http_response_code(201);
				echo json_encode(["message" => "Máquina adicionada no orçamento."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
		
		// "PUT /v1/budget/machine/([\w-]+)" 
		public function update_budget_machine($budget_machine_uuid) {

			validate_token();

			$data = validate_payload( ["machine_uuid"]);

			$query = "UPDATE budget_machine SET machine_id = (SELECT id FROM machine WHERE uuid = ?) WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ss', $data->machine_uuid, $budget_machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Máquina do orçamento atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar máquina do orçamento."]);
			}
		}

		public function update_budget_machine_price($budget_machine_uuid) {

			validate_token();

			$data = validate_payload( []);

			$query = "UPDATE budget_machine SET price_per_hour = ?, price_per_distance = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('iis', $data->price_per_hour, $data->price_per_distance, $budget_machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Valores da máquina do orçamento atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar valores máquina do orçamento."]);
			}
		}

        // "DELETE /v1/budget/(\w+)"
		public function delete_budget_machine($budget_machine_uuid) {
			validate_token();
			
			$query = "UPDATE budget_machine SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Máquina desconectada do orçamento."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desconectar máquina do orçamento."]);
			}
		}
		
	}