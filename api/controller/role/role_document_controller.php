<?php
	require_once('./service/utils.php');

	// {
	// 	"name": "romario",
	// 	"type_uuid": "hIFqedcnWKwiQgVV7rUVeC16g1HNiEY3cNPYyCn1n0hQjMA8q46oGz28l7hMwHQN",
	// 	"role_uuid": "9QMeKUMc89xCgCWbQIMezCDmm5VcySrj"
	// }

	class RoleDocumentController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        # "GET /v1/role/document/" 	
		public function get_role_document() {
			$query = "SELECT * FROM role_docs";
			$result = $this->mysqli->query($query);

			if ($result) {
				$role_docs = [];
				while ($row = $result->fetch_assoc()) {
					$role_docs[] = $row;
				}
				echo json_encode(["role_docs" => $role_docs]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        # "GET /v1/role/document/(\w+)" 
		public function get_role_document_by_uuid() {
			$token = validate_token();

			$query = "SELECT * FROM role_docs WHERE uuid = ? ";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$role_docs = [];
			while ($row = $result->fetch_assoc()) {
				$role_docs[] = $row;
			}

			echo json_encode(["role_docs" => $role_docs]);
		}

        # "POST /v1/role/document/" 	
		public function create_role_document() {
		    $token = validate_token();

            $data = validate_payload(["name", "type_uuid", "role_uuid"]);


			$uuid = generate_uuid_v3(64);
			$query = "INSERT INTO role_docs (`uuid`, `name`, `type_id`, `role_id`)
						VALUES (?, ? ,(SELECT id FROM document_type WHERE uuid = ? ), (SELECT id FROM role WHERE uuid = ? ))";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ssss', $uuid, $data->name, $data->type_uuid, $data->role_uuid );

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Documento cadastrado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar documento."]);
			}
		}

        # "PUT /v1/role/document/(\w+)" 	
		public function update_role_document($role_docs_uuid) {
            $data = validate_payload(["name"]);

			$query = "UPDATE role_docs SET `name` = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ss', $data->name, $role_docs_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Documento atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar documento."]);
			}
		}

        # "DELETE /v1/role/document/(\w+)"
		public function delete_role_document($role_docs_uuid) {
			$query = "UPDATE role_docs SET is_active = 0 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $role_docs_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Documento desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar documento."]);
			}
		}

        # "PUT /v1/role/document/reactivate/(\w+)" 
		public function reactivate_role_document($role_docs_uuid) {
			$query = "UPDATE role_docs SET is_active = 1 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $role_docs_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Documento reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar documento."]);
			}
		}
    }
