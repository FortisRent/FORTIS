<?php
	require_once('./service/utils.php');

	class ClientController {

		private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

		// "GET /v1/client/company/([\w-]+)"
		public function get_clients_by_company_uuid($company_uuid) {
            validate_token();

            try {
                $query = "	SELECT c.id, c.uuid, comp.name AS company_name, c.name, c.email, c.phone, c.cpf, DATE_FORMAT(c.created_at, '%d/%m/%Y') as created_at,
                                    c.neighborhood, city.name as city_name, gs.name as state_name
                            FROM client c
                            INNER JOIN company comp ON c.company_id = comp.id
                            LEFT JOIN city city ON c.city_id = city.id
                            LEFT JOIN geo_state gs ON city.state_id = gs.id
                            WHERE comp.uuid = ? AND c.deleted_at IS NULL";
                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $company_uuid);
                $stmt->execute();
                $result = $stmt->get_result();

                $clients = [];
                while ($row = $result->fetch_assoc()) {
                    $clients[] = $row;
                }

                echo json_encode(["clients" => $clients]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		// "GET /v1/client/([\w-]+)"
		public function get_client_by_uuid($client_uuid) {
			$token = validate_token();
            try {
                $query = "  SELECT c.id, c.uuid, comp.name AS company_name, c.name, c.email, c.phone, c.cpf, DATE_FORMAT(c.created_at, '%d/%m/%Y') as created_at,
                                    c.neighborhood, city.name as city_name, gs.name as state_name
                            FROM client c
                            INNER JOIN company comp ON c.company_id = comp.id
                            LEFT JOIN city city ON c.city_id = city.id
                            LEFT JOIN geo_state gs ON city.state_id = gs.id
                            WHERE c.uuid = ? AND c.deleted_at IS NULL";
                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $client_uuid);
                $stmt->execute();
                $result = $stmt->get_result();

                $client = $result->fetch_assoc();

                if (!$client) {
                    http_response_code(404);
                    echo json_encode(["error" => "client not found"]);
                    exit();
                }

                echo json_encode(["client" => $client]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		// "POST /v1/client/"
        public function create_client()
        {
            $data = validate_payload(["company_uuid", "name"]);

            $stmt = $this->mysqli->prepare("INSERT INTO client (company_id, name, cpf, phone, email, city_id, neighborhood) 
                                            VALUES ((SELECT id FROM company WHERE uuid = ?), ?, ?, ?, ?, (SELECT id FROM city WHERE name = ?), ?);");

            $uuid = random_str(32);

            $stmt->bind_param('sssssss', $data->company_uuid, $data->name, $data->cpf, $data->phone, $data->email, $data->city_name, $data->neighborhood);

            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message" => "Cliente cadastrado."]);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao cadastrar cliente."]);
                exit();
            }
        }

		// "PUT /v1/client/([\w-]+)"
		public function update_client($client_uuid){
			$token = validate_token();

			$data = validate_payload(["name"]);

            $stmt = $this->mysqli->prepare("UPDATE client SET name = ?, cpf = ?, phone = ?, email = ?, city_id = (SELECT id FROM city WHERE name = ?), neighborhood = ? WHERE uuid = ?");

			$stmt->bind_param('sssssss', $data->name, $data->cpf, $data->phone, $data->email,$data->city_name, $data->neighborhood, $client_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Cliente atualizado."]);
				exit();
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar cliente."]);
				exit();
			}
		}

		// "DELETE /v1/client/([\w-]+)"
		public function delete_client($client_uuid) {

            $deleteQuery = "UPDATE client SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
            $deleteStmt = $this->mysqli->prepare($deleteQuery);
            $deleteStmt->bind_param('s', $client_uuid);

            if ($deleteStmt->execute()) {
                http_response_code(200);
                echo json_encode(["message" => "Cliente desativado."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao deletar cliente: " . $this->mysqli->error]);
            }

            $this->mysqli->close();
		}

		// "PUT /v1/client/reactivate/([\w-]+)"
		public function reactivate_client($client_uuid) {

			$reactivateQuery = "UPDATE client SET deleted_at = NULL WHERE uuid = ?";
			$reactivateStmt = $this->mysqli->prepare($reactivateQuery);
			$reactivateStmt->bind_param('s', $client_uuid);

			if ($reactivateStmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Cliente reativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao reativar cliente: " . $this->mysqli->error]);
			}

			$this->mysqli->close();
		}


	}