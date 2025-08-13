<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class OperatorController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		//  "GET /v1/operator/company/(\w+)
		public function get_operator_by_company_uuid($company_uuid) {

            $token = validate_token();

			$query = " SELECT e.uuid, c.uuid as company_uuid, u.full_name AS employee_name, c.name AS company_name, r.name as role_name, e.is_invite_accepted, DATE_FORMAT(e.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee e
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
								WHERE c.uuid = ? AND e.role_id = 3 AND e.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$operators_company = [];
			while ($row = $result->fetch_assoc()) {
				$operators_company[] = $row;
			}

			echo json_encode(["operators_company" => $operators_company]);
		}

		//  "GET /v1/operator/company/([\w-]+)
		public function get_operator_by_company($company_uuid) {

            $token = validate_token();

			$query = " SELECT e.uuid, c.uuid as company_uuid, u.full_name AS employee_name, c.name AS company_name, r.name as role_name, (r.hourly_price/100) AS hourly_price, (r.salary/100) AS salary, e.is_invite_accepted, DATE_FORMAT(e.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee e
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
								INNER JOIN role r ON e.role_id = r.id
								WHERE c.uuid = ? AND e.role_id = 3 AND e.deleted_at IS NULL AND e.is_invite_accepted = 1;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$company_operators = [];
			while ($row = $result->fetch_assoc()) {
				$row['salary'] = isset($row['salary']) ? number_format($row['salary'], 2, ',', '.') : null;
				$row['hourly_price'] = isset($row['hourly_price']) ? number_format($row['hourly_price'], 2, ',', '.') : null;
				$company_operators[] = $row;
			}

			echo json_encode(["company_operators" => $company_operators]);
		}

      // Fazer endpoint para criar comentários do operador e afins.


	//   "GET /v1/operator/checks/([\w-]+)" 
	  public function get_operator_checks($employee_uuid) {

		$token = validate_token();

		$query = " SELECT 	e.uuid, u.full_name AS employee_name, u.phone, u.email, r.name as role_name, m.name AS machine_name, oci.description AS checkin_description, 
											DATE_FORMAT(oci.created_at, '%H:%i') AS checkin_hour, DATE_FORMAT(oci.created_at, '%d/%m/%Y') AS checkin_date,
											oco.description AS checkout_description, DATE_FORMAT(oco.created_at, '%H:%i') AS checkout_hour, DATE_FORMAT(oco.created_at, '%d/%m/%Y') AS checkout_date, p.identifier AS project_identifier, p.name AS project_name
							FROM employee e
							INNER JOIN user u ON e.user_id = u.id
							INNER JOIN budget_machine_operator bmo ON bmo.employee_id = e.id
							INNER JOIN budget_machine bm ON bmo.budget_machine_id = bm.id
							INNER JOIN machine m ON bm.machine_id = m.id
							LEFT JOIN  operator_check_in oci ON oci.machine_operator_id = bmo.id
							LEFT JOIN operator_check_out oco ON oco.machine_operator_id = bmo.id
							INNER JOIN role r ON e.role_id = r.id
							INNER JOIN budget b ON bm.budget_id = b.id
							INNER JOIN project p ON b.project_id = p.id
							WHERE e.uuid = ? AND e.deleted_at IS NULL;";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $employee_uuid);
		$stmt->execute();
		$result = $stmt->get_result();

		$operator_checks = [];
		while ($row = $result->fetch_assoc()) {
			$operator_checks[] = $row;
		}

		echo json_encode(["operator_checks" => $operator_checks]);
	}
		
	}