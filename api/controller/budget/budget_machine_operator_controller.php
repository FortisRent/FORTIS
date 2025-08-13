<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class BudgetMachineOperatorController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/budget/machine/operator/(\w+)"
		public function get_budget_machine_operator_by_uuid($budget_machine_operator_uuid) {
			$query = "  SELECT 	bmo.uuid AS budget_machine_operator_uuid, bm.uuid AS budget_machine_uuid, e.uuid AS employee_uuid, u.full_name AS employee_name, p.name AS project_name, p.description,
												pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, c.name AS city_name, gs.name AS state_name, m.name AS machine_name, m.license_plate, m.serial_number,
												m.chassis_number, b.uuid AS budget_uuid, DATE_FORMAT(oci.created_at, '%d/%m/%Y %H:%i') AS checkin_date, oci.description AS checkin_description, DATE_FORMAT(oco.created_at, '%d/%m/%Y %H:%i') AS checkout_date, oco.description AS checkout_description
								FROM budget_machine_operator bmo
								INNER JOIN budget_machine bm ON bmo.budget_machine_id = bm.id
								INNER JOIN budget b ON bm.budget_id = b.id
								INNER JOIN employee e ON bmo.employee_id = e.id
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN project_address pa ON pa.project_id = p.id
								INNER JOIN city c ON pa.city_id = c.id
								INNER JOIN geo_state gs ON c.state_id = gs.id
								LEFT JOIN operator_check_in oci ON oci.machine_operator_id = bmo.id
								LEFT JOIN operator_check_out oco ON oco.machine_operator_id = bmo.id
                                WHERE bmo.uuid = ? AND bmo.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_machine_operator_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "budget not found"]);
				exit();
			}

			$budget_machine_operator = $result->fetch_assoc();
			echo json_encode(["budget_machine_operator" => $budget_machine_operator]);
		}

		public function get_budget_by_logged() {
			$token = validate_token();

			$query = "  SELECT 	bmo.uuid AS budget_machine_operator_uuid, bm.uuid AS budget_machine_uuid, e.uuid AS employee_uuid, p.uuid AS project_uuid, p.name AS project_name, 
												u.full_name AS employee_name, b.uuid AS budget_uuid, DATE_FORMAT(b.expected_date, '%d/%m/%Y') AS expected_date,
												cli.full_name AS client_name, cli.phone AS client_phone, m.name AS machine_name, pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, ci.name AS city_name, gs.name AS state_name,
												DATE_FORMAT(oci.created_at, '%H:%i') AS checkin_hour,  DATE_FORMAT(oci.created_at, '%d/%m/%Y') AS checkin_date, DATE_FORMAT(oco.created_at, '%H:%i') AS checkout_hour,  DATE_FORMAT(oco.created_at, '%d/%m/%Y') AS checkout_date
								FROM budget_machine_operator bmo
								INNER JOIN budget_machine bm ON bmo.budget_machine_id = bm.id
								INNER JOIN budget b ON bm.budget_id = b.id
								INNER JOIN employee e ON bmo.employee_id = e.id
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN project_address pa ON pa.project_id = p.id
								INNER JOIN city ci ON pa.city_id = ci.id
								INNER JOIN geo_state gs ON ci.state_id = gs.id
								INNER JOIN user cli ON p.user_id = cli.id
								LEFT JOIN operator_check_in oci ON oci.machine_operator_id = bmo.id
								LEFT JOIN operator_check_out oco ON oco.machine_operator_id = bmo.id
                                WHERE u.uuid = ? AND bmo.deleted_at IS NULL AND b.deleted_at IS NULL AND b.expected_date IS NOT NULL;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result) {
				$budget_machine = [];
				while ($row = $result->fetch_assoc()) {
					$budget_machine[] = $row;
					
				}
				echo json_encode(["budget_machine" => $budget_machine]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		// "GET /v1/budget/machine/logged/checkin/"
		public function get_budget_has_checkin() {
			$token = validate_token();

			$query = "  SELECT 	bmo.uuid AS budget_machine_operator_uuid, bm.uuid AS budget_machine_uuid, e.uuid AS employee_uuid, u.uuid AS user_employee_uuid, p.uuid AS project_uuid, p.name AS project_name, 
												u.full_name AS employee_name, b.uuid AS budget_uuid, DATE_FORMAT(b.expected_date, '%d/%m/%Y') AS expected_date,
												cli.full_name AS client_name, cli.phone AS client_phone, m.name AS machine_name, pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, ci.name AS city_name, gs.name AS state_name,
												oci.description, DATE_FORMAT(oci.created_at, '%H:%i') AS checkin_hour,  DATE_FORMAT(oci.created_at, '%d/%m/%Y') AS checkin_date, DATE_FORMAT(oco.created_at, '%H:%i') AS checkout_hour,  DATE_FORMAT(oco.created_at, '%d/%m/%Y') AS checkout_date
								FROM budget_machine_operator bmo
								INNER JOIN budget_machine bm ON bmo.budget_machine_id = bm.id
								INNER JOIN budget b ON bm.budget_id = b.id
								INNER JOIN employee e ON bmo.employee_id = e.id
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN project p ON b.project_id = p.id
								INNER JOIN project_address pa ON pa.project_id = p.id
								INNER JOIN city ci ON pa.city_id = ci.id
								INNER JOIN geo_state gs ON ci.state_id = gs.id
								INNER JOIN user cli ON p.user_id = cli.id
								INNER JOIN (
									SELECT machine_operator_id, MIN(created_at) AS last_checkin
									FROM operator_check_in
									GROUP BY machine_operator_id
								) oci_latest ON oci_latest.machine_operator_id = bmo.id
								INNER JOIN operator_check_in oci ON oci.machine_operator_id = oci_latest.machine_operator_id AND oci.created_at = oci_latest.last_checkin
								LEFT JOIN operator_check_out oco ON oco.machine_operator_id = bmo.id
                                WHERE u.uuid = ? AND bmo.deleted_at IS NULL AND b.deleted_at IS NULL;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result) {
				$budget_machine = [];
				while ($row = $result->fetch_assoc()) {
					$budget_machine[] = $row;
					
				}
				echo json_encode(["budget_machine" => $budget_machine]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        // "POST /v1/budget/machine/operator/"
		public function create_budget_machine_operator() {
			validate_token();
			$data = validate_payload(["budget_machine_uuid","employee_list"]);

			$this->mysqli->begin_transaction();
		
			try {
				$query = "INSERT INTO budget_machine_operator (budget_machine_id, role_id)
								 VALUES ((SELECT id FROM budget_machine WHERE uuid = ?), (SELECT id FROM `role` WHERE uuid = ?))";
				$stmt = $this->mysqli->prepare($query);

                foreach ($data->employee_list as $employee) {
                    $stmt->bind_param('ss', $data->budget_machine_uuid, $employee->role_uuid);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Erro ao adicionar cargo a máquina: $employee->role_uuid");
                    }
                }
		
				$this->mysqli->commit();
				
				http_response_code(201);
				echo json_encode(["message" => "Funcionários adicionado a máquina do orçamento."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}

		// public function create_budget_machine_operator() {
		// 	$token = validate_token();
		// 	$data = validate_payload(["budget_machine_uuid","employee_list"]);

		// 	$this->mysqli->begin_transaction();
		
		// 	try {
		// 		$query = "INSERT INTO budget_machine_operator (budget_machine_id, employee_id)
		// 						 VALUES ((SELECT id FROM budget_machine WHERE uuid = ?), (SELECT id FROM employee WHERE uuid = ?))";
		// 		$stmt = $this->mysqli->prepare($query);

        //         foreach ($data->employee_list as $employee) {
        //             $stmt->bind_param('ss', $data->budget_machine_uuid, $employee->employee_uuid);
                    
        //             if (!$stmt->execute()) {
        //                 throw new Exception("Erro ao adicionar operador ao orçamento: $employee->employee_uuid");
        //             }
        //         }
		
		// 		$this->mysqli->commit();
				
		// 		http_response_code(201);
		// 		echo json_encode(["message" => "Funcionários adicionado a máquina do orçamento."]);
		
		// 	} catch (Exception $e) {
		// 		$this->mysqli->rollback();
		// 		http_response_code(500);
		// 		echo json_encode(["message" => $e->getMessage()]);
		// 	}
		// }
		
		// "PUT /v1/budget/machine/operator/([\w-]+)" 
		public function update_budget_machine_operator($budget_machine_operator_uuid) {

			validate_token();

			$data = validate_payload( ["employee_uuid"]);

			$query = "UPDATE budget_machine_operator SET employee_id = (SELECT id FROM employee WHERE uuid = ?) WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ss', $data->employee_uuid, $budget_machine_operator_uuid);
 
			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Operador adicionado a máquina."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao adicionar máquina do operador."]);
			}
		}

        // "DELETE /v1/budget/machine/operator/(\w+)"
		public function delete_budget_machine_operator($budget_machine_operator_uuid) {
			validate_token();
			
			$query = "UPDATE budget_machine_operator SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_machine_operator_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Operador desconectado da máquina."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desconectar operator da máquina."]);
			}
		}
		
	}