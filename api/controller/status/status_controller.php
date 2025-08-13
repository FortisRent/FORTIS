<?php
	require_once('./service/utils.php');

	// {
	// 	"name": "Médico",
	// 	"permission": 1,
	// }

	class StatusController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		public function get_status() {
			$query = "SELECT * FROM status";
			$result = $this->mysqli->query($query);

			if ($result) {
				$status = [];
				while ($row = $result->fetch_assoc()) {
					$status[] = $row;
				}
				echo json_encode(["status" => $status]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		public function get_status_by_uuid($status_uuid) {
			$query = "SELECT * FROM status WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $status_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "status not found"]);
				exit();
			}

			$status = $result->fetch_assoc();
			echo json_encode(["status" => $status]);
		}

		public function create_status() {
			$token = validate_token();

			$data = validate_payload(["name"]);

			$query = "INSERT INTO status (`name`) VALUES (?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $data->name);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Status cadastrado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar status."]);
			}
		}

		public function update_status($status_uuid) {
			$data = validate_payload(["name"]);

			$query = "UPDATE status SET `name` = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ss', $data->name, $status_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Status atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar status."]);
			}
		}

		public function delete_status($status_uuid) {
			$query = "UPDATE status SET is_active = 0 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $status_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Status desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar status."]);
			}
		}

		public function reactivate_status($status_uuid) {
			$query = "UPDATE status SET is_active = 1 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $status_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Status reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar status."]);
			}
		}
    }
