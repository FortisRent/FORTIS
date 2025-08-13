<?php
	require_once('./service/utils.php');

	// {
    //     "name": "CRM", 
    // }

	class DocumentTypeController {
		private $mysqli;
        
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        public function get_document_type() {
			$query = " SELECT * FROM document_type ";

			$result = $this->mysqli->query($query);

			if ($result) {
				$document_type = [];
				while ($row = $result->fetch_assoc()) {
					$document_type[] = $row;
				}
				echo json_encode(["document_type" => $document_type]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        public function get_document_type_by_uuid($document_type_uuid) {
			$query = "SELECT * FROM document_type WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $document_type_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "document_type not found"]);
				exit();
			}

			$document_type = $result->fetch_assoc();
			echo json_encode(["document_type" => $document_type]);
		}

		public function create_document_type() {
			$token = validate_token();

            $data = validate_payload(["name"]);

			$uuid = generate_uuid_v3(64);

			$query = "INSERT INTO document_type (`uuid`, `name`)
						VALUES (?, ?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ss', $uuid, $data->name);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Documento cadastrado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar documento."]);
			}
		}

		public function update_document_type($document_type_uuid) {

            validate_token();

			$data = validate_payload( ["name"]);

			$query = "UPDATE document_type SET `name` = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ss', $data->name, $document_type_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Documento atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar documento."]);
			}
		}

		public function delete_document_type($document_type_uuid) {
			$query = "UPDATE document_type SET is_active = 0 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $document_type_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Documento desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar documento."]);
			}
		}

		public function reactivate_document_type($document_type_uuid) {
			$query = "UPDATE document_type SET is_active = 1 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $document_type_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Documento reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar documento."]);
			}
		}
	}