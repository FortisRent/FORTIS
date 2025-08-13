<?php
	require_once('./service/utils.php');

	// {
	// 	"name": "Médico",
	// 	"permission": 1,
	// }

	class RoleController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		public function get_role() {
			$query = " SELECT r.uuid, r.id, r.name, (r.salary/100) AS salary, (r.hourly_price/100) AS hourly_price
								FROM `role` r ";
			$result = $this->mysqli->query($query);

			if ($result) {
				$role = [];
				while ($row = $result->fetch_assoc()) {
					$row['salary'] = number_format($row['salary'], 2, ',', '.');
					$row['hourly_price'] = number_format($row['hourly_price'], 2, ',', '.');
					$role[] = $row;
				}
				echo json_encode(["roles" => $role]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		public function get_role_by_company_uuid($company_uuid) {
			$token = validate_token();

			$query = "	SELECT r.uuid, r.id, r.name, (r.salary/100) AS salary, (r.hourly_price/100) AS hourly_price
								FROM `role` r 
								WHERE r.company_id = (SELECT id FROM company WHERE uuid = ?) AND r.deleted_at IS NULL";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$role = [];
			while ($row = $result->fetch_assoc()) {
				$row['salary'] = $row['salary'] !== null ? number_format($row['salary'], 2, ',', '.') : null;
				$row['hourly_price'] = $row['hourly_price'] !== null ? number_format($row['hourly_price'], 2, ',', '.') : null;

				$role[] = $row;
			}

			echo json_encode(["roles" => $role]);
		}

		public function get_role_by_uuid($role_uuid) {
			$query = "SELECT SELECT r.uuid, r.id, r.name, (r.salary/100) AS salary, (r.hourly_price/100) AS hourly_price
								FROM `role` r  
								WHERE r.uuid = ? AND r.deleted_at IS NULL";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $role_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "roles not found"]);
				exit();
			}
		
			$role = $result->fetch_assoc();
		
			$role['salary'] = $role['salary'] !== null ? number_format($role['salary'], 2, ',', '.') : null;
			$role['hourly_price'] = $role['hourly_price'] !== null ? number_format($role['hourly_price'], 2, ',', '.') : null;

		
			echo json_encode(["role" => $role]);
		}

		public function create_role() {
			$token = validate_token();

			$data = validate_payload(["name", "company_uuid"]);

			$query = "INSERT INTO role (`name`, company_id, salary, hourly_price) VALUES (?, (SELECT id FROM company WHERE uuid = ?), ?, ?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ssii', $data->name, $data->company_uuid, $data->salary, $data->hourly_price);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Cargo cadastrado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar cargo."]);
			}
		}

		public function update_role($role_uuid) {
			$data = validate_payload([]);

			$query = "UPDATE role SET salary = ?, hourly_price = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('iis',  $data->salary, $data->hourly_price, $role_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Cargo atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar cargo."]);
			}
		}

		public function delete_role($role_uuid) {
			$query = "UPDATE role SET is_active = 0 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $role_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Cargo desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar cargo."]);
			}
		}

		public function reactivate_role($role_uuid) {
			$query = "UPDATE role SET is_active = 1 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $role_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Cargo reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar cargo."]);
			}
		}
    }
