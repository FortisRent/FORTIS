<?php
	require_once('./service/utils.php');

	// {
	// 	"user_uuid": "ronaldinho soccer 98",
	// 	"error": "erro do urubu do pix",
    // 	"message": "deu ruim , aceite"
	// }

	class LogStageController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}
		 # "GET /v1/log/stage/" 
		public function get_log_stage() {
			$query = " SELECT * FROM log_stage ";

			$result = $this->mysqli->query($query);

			if ($result) {
				$log_stage = [];
				while ($row = $result->fetch_assoc()) {
					$log_stage[] = $row;
				}
				echo json_encode(["log_stage" => $log_stage]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        # "GET /v1/log/stage/(\w+)" 	 
		public function get_log_stage_by_uuid($log_stage_uuid) {
			$token = validate_token();

			$query = "SELECT * FROM log_stage WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $log_stage_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$log_stage = [];
			while ($row = $result->fetch_assoc()) {
				$log_stage[] = $row;
			}

			echo json_encode(["log_stages" => $log_stage]);
		}

        # "POST /v1/log/stage/" 
		public function create_log_stage() {
			$token = validate_token();

            $data = validate_payload(["user_uuid", "error", "message"]);

			$uuid = generate_uuid_v3(64);
			$query = "INSERT INTO log_stage (`uuid`, `user_id`, `error`, `message`)
						VALUES (?,(SELECT id FROM user WHERE uuid = ? ), ?, ?)"; 
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ssss', $uuid, $data->user_uuid, $data->error, $data->message);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Registro cadastrado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar registro."]);
			}
		}

        # "PUT /v1/log/stage/(\w+)" 
		public function update_log_stage($log_stage_uuid) {

            $data = validate_payload(["error","message"]);

			$query = "UPDATE log_stage SET `error` = ? , `message` = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('sss', $data->error, $data->message, $log_stage_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Registro atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar registro."]);
			}
		}

        # "DELETE /v1/log/stage/(\w+)"
		public function delete_log_stage($log_stage_uuid) {
			$query = "UPDATE log_stage SET is_active = 0 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $log_stage_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Registro desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar registro."]);
			}
		}

        # "PUT /v1/log/stage/reactivate/(\w+)"   
		public function reactivate_log_stage($log_stage_uuid) {
			$query = "UPDATE log_stage SET is_active = 1 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $log_stage_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Registro reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar registro."]);
			}
		}
	}