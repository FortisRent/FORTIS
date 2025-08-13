<?php
	require_once('./service/utils.php');

    // {
    //     "email": "capivara@gmail.com",
    //     "company_uuid": "8QAGQyJpwPjIsNYBETybuV7qCbnZOajF1GnHooqAH1KziLBFJOmTwoVyNbJMaJJP",
    //     "role_name": "Financeiro"
    // }

	class EmployeeController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		public function get_employees() {
			$query = "	SELECT e.uuid, c.uuid as company_uuid, u.full_name AS employee_name, c.name AS company_name, r.name as role_name, (r.hourly_price/100) AS hourly_price, (r.salary/100) AS salary, e.ctps_number, e.is_invite_accepted, DATE_FORMAT(e.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee e
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
								WHERE e.deleted_at IS NULL; ";

			$result = $this->mysqli->query($query);

			if ($result) {
				$employee = [];
				while ($row = $result->fetch_assoc()) {
					$employee[] = $row;
				}
				echo json_encode(["employee" => $employee]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		public function get_employee_by_logged() {

			$token = validate_token();

			$query = "	SELECT e.uuid, c.uuid as company_uuid, u.full_name AS employee_name, c.name AS company_name, r.name as role_name, (r.hourly_price/100) AS hourly_price, (r.salary/100) AS salary, e.ctps_number, e.is_invite_accepted, DATE_FORMAT(e.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee e
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
								WHERE e.user_id = (SELECT id FROM user WHERE uuid = ?) AND e.is_invite_accepted = 1 AND e.deleted_at IS NULL;";


			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$employee = [];

			while ($row = $result->fetch_assoc()) {
				$employee[] = $row;
			}

			echo json_encode(["employees" => $employee]);
		}

		public function get_employee_by_company_uuid($company_uuid) {


			$query = "	SELECT e.uuid as employee_uuid, c.uuid as company_uuid, u.full_name AS employee_name, u.phone,  c.name AS company_name, r.name as role_name, (r.hourly_price/100) AS hourly_price, e.ctps_number,
								(e.distance_amount/100) AS distance_amount, DATE_FORMAT(e.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee e
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
								WHERE c.uuid = ? AND e.is_invite_accepted = 1 AND e.deleted_at IS NULL;";


			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$employee = [];

			while ($row = $result->fetch_assoc()) {
				$row['hourly_price'] = number_format($row['hourly_price'], 2, ',', '.');
				$row['distance_amount'] = number_format($row['distance_amount'], 2, ',', '.');
				$row['salary'] = number_format($row['salary'], 2, ',', '.');
				$employee[] = $row;
			}

			echo json_encode(["employees" => $employee]);
		}

		public function get_employee_by_uuid($employee_uuid) {

			$query = "	SELECT e.uuid, c.uuid as company_uuid, u.full_name AS employee_name, c.name AS company_name, r.name as role_name, (r.hourly_price/100) AS hourly_price, (r.salary/100) AS salary, e.ctps_number, e.distance_amount, e.is_invite_accepted, ua.uuid as address_uuid, ua.zip_code, ua.street, ua.number_street,
												ua.complement, ua.neighborhood, city.name as city_name, gs.name as state_name, DATE_FORMAT(e.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee e
								INNER JOIN user u ON e.user_id = u.id
								LEFT JOIN user_address ua ON ua.user_id = u.id
								LEFT JOIN city city ON ua.city_id = c.id
								LEFT JOIN geo_state gs ON c.state_id = gs.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
								WHERE e.uuid = ? AND e.deleted_at IS NULL";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Funcionário não encontrado"]);
				exit();
			}

			$employee = $result->fetch_assoc();
			echo json_encode(["employee" => $employee]);
		}

		public function create_employee() {

			validate_token();

			$data = validate_payload(["cpf", "company_uuid", "role_name"]);

			try {
				// Verifica se o funcionario tem um cpf válido.
				$query3 = "SELECT cpf FROM user WHERE cpf = ?";
				$stmt3 = $this->mysqli->prepare($query3);
				$stmt3->bind_param('s', $data->cpf);
				$stmt3->execute();
				$stmt3->store_result();

				if ($stmt3->num_rows == 0) {
					http_response_code(409);
					echo json_encode(["message" => "CPF não cadastrado, por favor finalize o cadastro do funcionário no sistema."]);
					exit();
				}
				
				// Se existir, cadastra o funcionário com um convite por aceitar.
				$query = "  INSERT INTO employee (`user_id`, `company_id`, `role_id`, `hourly_price`, `ctps_number`, `salary`, `minimum_rental_period`, `distance_amount`)
									SELECT 
											(SELECT id FROM user WHERE cpf = ?), 
											(SELECT id FROM company WHERE uuid = ?), 
											(SELECT id FROM role WHERE name = ?), ?, ?, ?, ?, ?
									WHERE NOT EXISTS (
										SELECT 1 
										FROM employee
										WHERE `user_id` = (SELECT id FROM user WHERE cpf = ?)
											AND `company_id` = (SELECT id FROM company WHERE uuid = ?)
											AND `role_id` = (SELECT id FROM role WHERE name = ?)
											AND deleted_at IS NULL
									);";
							
				$stmt = $this->mysqli->prepare($query);
        
				$stmt->bind_param('sssisiiisss',$data->cpf, $data->company_uuid, $data->role_name, $data->hourly_price, $data->ctps_number, $data->salary, $data->minimum_rental_period, $data->distance_amount, $data->cpf, $data->company_uuid, $data->role_name);

				
				if ($stmt->execute() && $stmt->affected_rows > 0) {
					http_response_code(201);
					echo json_encode(["message" => "Convite enviado."]);
				} else {
					http_response_code(502);
					echo json_encode(["message" => "Erro ao cadastrar funcionário pois ele já possui cadastro nessa empresa."]);
				}
			} catch (Exception $e) {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $e]);
			}
		}

		public function update_employee($employee_uuid) {

			validate_token();

			$data = validate_payload( ["ctps_number"]);

			$query = "UPDATE employee SET hourly_price = ?, ctps_number = ?, salary = ?, minimum_rental_period = ?, distance_amount = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('isiiis', $data->hourly_price, $data->ctps_number, $data->salary, $data->minimum_rental_period, $data->distance_amount, $employee_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Funcionário atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar funcionário."]);
			}
		}

		public function delete_employee($employee_uuid) {
			$query = "UPDATE employee SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Funcionário desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar funcionário."]);
			}
		}

		public function reactivate_employee($employee_uuid) {
			$query = "UPDATE employee SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Funcionário reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar funcionário."]);
			}
		}

		public function get_invite_by_logged_user() {
			$token = validate_token();

			$query = "SELECT e.id, e.uuid, e.is_invite_accepted,  u.full_name as user_name, c.name as company_name, r.name as role_name, (r.hourly_price/100) AS hourly_price, (r.salary/100) AS salary, e.ctps_number, DATE_FORMAT(e.created_at, '%d/%m/%Y') as created_at 
								FROM employee e
								INNER JOIN user u ON e.user_id = u.id 
								INNER JOIN company c ON e.company_id = c.id
								INNER JOIN role r ON e.role_id = r.id
								WHERE e.user_id = (SELECT id FROM user WHERE uuid = ?) AND e.deleted_at IS NULL" ;

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$invites = [];

			while ($row = $result->fetch_assoc()) {
				$invites[] = $row;
			}

			echo json_encode(["invites" => $invites]);
		}

		public function accept_invite($employee_uuid) {
			$query = "UPDATE employee SET is_invite_accepted = 1 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Convite aceito."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao aceitar convite."]);
			}	
		}

		public function decline_invite($employee_uuid) {
			$query = "DELETE FROM employee WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Convite recusado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao recusar convite."]);
			}
		}

	}