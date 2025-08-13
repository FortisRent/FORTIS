<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class OperatorBudgetController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/operator/budget/(\w+)"
		public function get_operators_by_budget_uuid($budget_uuid) {
			$query = "  SELECT ob.uuid as op_budget_uuid, b.name as budget_name, p.name as project_name, op.full_name as operator_name, m.name as machine_name, c.name as company_name, s.name as status_name
								FROM operator_budget ob
                                INNER JOIN employee e ON ob.employee_id = e.id
								INNER JOIN budget b ON ob.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN user op ON e.user_id = op.id
								INNER JOIN budget_machine bm ON bm.budget_id = b.id
								INNER JOIN machine m ON bm.machine_id = m.id
                                INNER JOIN company c ON m.company_id = c.id
								INNER JOIN budget_history bh ON bh.budget_id = b.id
								INNER JOIN status s ON bh.status_id = s.id
								WHERE b.uuid = ? AND ob.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "budget not found"]);
				exit();
			}

			$budget = $result->fetch_assoc();
			echo json_encode(["budget" => $budget]);
		}

        //  "GET /v1/operator/budget/project/(\w+)
		public function get_operators_by_project_uuid($project_uuid) {

            $token = validate_token();

			$query = "  SELECT ob.uuid as op_budget_uuid, b.name as budget_name, p.name as project_name, op.full_name as operator_name, m.name as machine_name, c.name as company_name, s.name as status_name
								FROM operator_budget ob
                                INNER JOIN employee e ON ob.employee_id = e.id
								INNER JOIN budget b ON ob.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN user op ON e.user_id = op.id
								INNER JOIN budget_machine bm ON bm.budget_id = b.id
								INNER JOIN machine m ON bm.machine_id = m.id
                                INNER JOIN company c ON m.company_id = c.id
								INNER JOIN budget_history bh ON bh.budget_id = b.id
								INNER JOIN status s ON bh.status_id = s.id
								WHERE b.project_id= (SELECT id FROM project WHERE uuid = ?) AND ob.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$project_budgets = [];
			while ($row = $result->fetch_assoc()) {
				$project_budgets[] = $row;
			}

			echo json_encode(["project_budgets" => $project_budgets]);
		}

		// "GET /v1/operator/budget/logged/"
		public function get_projects_by_operator_logged() {
			$token = validate_token();

			$query = "  SELECT 	ob.uuid as op_budget_uuid, b.name as budget_name, p.name as project_name, op.full_name as operator_name, m.name as machine_name, c.name as company_name, s.name as status_name, 
												p.identifier, p.name as project_name, DATE_FORMAT(p.expected_date, '%d/%m/%Y') as expected_date, DATE_FORMAT(p.expected_date, '%W') as day_name, p.start_time, p.end_time, 
												u.full_name as client_name, pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, ct.name as city_name, gs.name as state_name
								FROM operator_budget ob
                                INNER JOIN employee e ON ob.employee_id = e.id
								INNER JOIN budget b ON ob.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
 								INNER JOIN project_address pa ON pa.project_id = p.id
  								INNER JOIN city ct ON pa.city_id = ct.id
                                INNER JOIN geo_state gs ON ct.state_id = gs.id
								INNER JOIN user op ON e.user_id = op.id
                                INNER JOIN user u ON p.user_id = u.id
								INNER JOIN budget_machine bm ON bm.budget_id = b.id
								INNER JOIN machine m ON bm.machine_id = m.id
                                INNER JOIN company c ON m.company_id = c.id
								INNER JOIN budget_history bh ON bh.budget_id = b.id
								INNER JOIN status s ON bh.status_id = s.id
								WHERE op.uuid = ? AND ob.deleted_at IS NULL AND p.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$operator_budget = [];
			while ($row = $result->fetch_assoc()) {
				$operator_budget[] = $row;
			}

			echo json_encode(["operator_budget" => $operator_budget]);
		}

		// "GET /v1/operator/budget/"
		public function get_projects_by_operator_uuid($operator_uuid) {
			$token = validate_token();

			$query = "  SELECT 	ob.uuid as op_budget_uuid, b.name as budget_name, p.name as project_name, op.full_name as operator_name, m.name as machine_name, c.name as company_name, s.name as status_name, 
												p.identifier, p.name as project_name, DATE_FORMAT(p.expected_date, '%d/%m/%Y') as expected_date, DATE_FORMAT(p.expected_date, '%W') as day_name, p.start_time, p.end_time, 
												u.full_name as client_name, pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, ct.name as city_name, gs.name as state_name
								FROM operator_budget ob
                                INNER JOIN employee e ON ob.employee_id = e.id
								INNER JOIN budget b ON ob.budget_id = b.id
								INNER JOIN project p ON b.project_id = p.id
 								INNER JOIN project_address pa ON pa.project_id = p.id
  								INNER JOIN city ct ON pa.city_id = ct.id
                                INNER JOIN geo_state gs ON ct.state_id = gs.id
								INNER JOIN user op ON e.user_id = op.id
                                INNER JOIN user u ON p.user_id = u.id
								INNER JOIN budget_machine bm ON bm.budget_id = b.id
								INNER JOIN machine m ON bm.machine_id = m.id
                                INNER JOIN company c ON m.company_id = c.id
								INNER JOIN budget_history bh ON bh.budget_id = b.id
								INNER JOIN status s ON bh.status_id = s.id
								WHERE op.uuid = ? AND ob.deleted_at IS NULL AND p.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $operator_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$operator_budget = [];
			while ($row = $result->fetch_assoc()) {
				$operator_budget[] = $row;
			}

			echo json_encode(["operator_budget" => $operator_budget]);
		}

        // "POST /v1/operator/budget/"
		public function create_operator_budget() {
			$token = validate_token();

			$data = validate_payload(["employee_uuid", "budget_uuid"]);

			$query = "INSERT INTO operator_budget ((SELECT id FROM employee WHERE uuid = ?), (SELECT id FROM budget WHERE uuid = ?)) VALUES (?, ?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ss', $data->employee_uuid, $data->budget_uuid);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Operator adicionado ao orçamento com sucesso."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao adicionar operador ao orçamento."]);
			}
		}

        // "DELETE /v1/operator/budget/(\w+)"
		public function delete_operator_budget($operator_budget_uuid) {

			$token = validate_token();
			
			$query = "UPDATE operator_budget SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $operator_budget_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Operador desativado do orçamento com sucesso."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desativar operador do orçamento."]);
			}
		}
		
	}