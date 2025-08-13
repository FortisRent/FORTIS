<?php
	require_once('./service/utils.php');

	class CompanyClientController {

		private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

		// "GET /v1/company/client/"
		public function get_company_clients_by_company_uuid($company_uuid) {
            $token = validate_token();

            try {
                $query = "	SELECT cc.uuid AS company_client_uuid, u.id, u.uuid AS user_uuid, comp.name AS company_name, u.full_name, u.email, u.phone, u.cpf, DATE_FORMAT(u.birthdate, '%d/%m/%Y') as birthdate, u.profile_picture_url, DATE_FORMAT(u.created_at, '%d/%m/%Y') as created_at,
									ua.uuid as address_uuid, ua.zip_code, ua.street, ua.number_street, ua.complement, ua.neighborhood, c.name as city_name, gs.name as state_name
									FROM company_client cc
                                    INNER JOIN company comp ON cc.company_id = comp.id
                                    INNER JOIN user u ON cc.user_id = u.id
									LEFT JOIN user_address ua ON ua.user_id = u.id
									LEFT JOIN city c ON ua.city_id = c.id
									LEFT JOIN geo_state gs ON c.state_id = gs.id
									WHERE comp.id = (SELECT id FROM company WHERE uuid = ?) AND cc.deleted_at IS NULL";
                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $company_uuid);
                $stmt->execute();
                $result = $stmt->get_result();

                $company_clients = [];
                while ($row = $result->fetch_assoc()) {
                    $company_clients[] = $row;
                }

                echo json_encode(["company_clients" => $company_clients]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		// "POST /v1/company/client/"
        public function create_company_client()
        {
            $data = validate_payload(["company_uuid", "full_name", "phone"]);
        
            try {
                $this->mysqli->begin_transaction();
        
                // Verifica se o telefone já está cadastrado
                $stmt1 = $this->mysqli->prepare("SELECT id FROM user WHERE phone = ?");
                $stmt1->bind_param('s', $data->phone);
                $stmt1->execute();
                $result1 = $stmt1->get_result();
        
                if ($result1->num_rows > 0) {
                    // Usuário já existe
                    $user = $result1->fetch_assoc();
                    $user_id = $user['id'];
                } else {
                    // Usuário não existe, cria um novo usuário e relaciona ele a client
                    $hashed_password = sha1($data->password ?? random_str(12));
                    $uuid = random_str(32);
        
                    $stmt2 = $this->mysqli->prepare("INSERT INTO user (uuid, full_name, password, cpf, phone, email, birthdate, profile_picture_url) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt2->bind_param('ssssssss',$uuid, $data->full_name, $hashed_password, $data->cpf, $data->phone, $data->email, $data->birthdate, $data->profile_picture_url);
        
                    if (!$stmt2->execute()) {
                        throw new Exception("Erro ao cadastrar usuário.");
                    }
        
                    $user_id = $this->mysqli->insert_id;
                }
        
                // Verifica se o vínculo já existe na tabela company_client
                $stmt3 = $this->mysqli->prepare("SELECT id FROM company_client WHERE user_id = ? AND company_id = (SELECT id FROM company WHERE uuid = ?)");
                $stmt3->bind_param('is', $user_id, $data->company_uuid);
                $stmt3->execute();
                $result2 = $stmt3->get_result();
        
                if ($result2->num_rows === 0) {
                    // Inserir na tabela company_client apenas se não existir
                    $stmt4 = $this->mysqli->prepare("INSERT INTO company_client (user_id, company_id) VALUES (?, (SELECT id FROM company WHERE uuid = ?))");
                    $stmt4->bind_param('is', $user_id, $data->company_uuid);
        
                    if (!$stmt4->execute()) {
                        throw new Exception("Erro ao cadastrar cliente.");
                    }
                }
        
                $this->mysqli->commit();
        
                http_response_code(201);
                echo json_encode(["message" => "Usuário e cliente vinculados com sucesso."]);
                exit();
            } catch (Exception $e) {
                $this->mysqli->rollback();
                http_response_code(500);
                echo json_encode(["error" => "Erro no banco de dados: " . $e->getMessage()]);
                exit();
            }
        }
        


		// "DELETE /v1/company/client/([\w-]+)"
		public function delete_company_client($company_client_uuid) {
            validate_token();

			// User exists, proceed with deactivation
			$deleteQuery = "UPDATE company_client SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$deleteStmt = $this->mysqli->prepare($deleteQuery);
			$deleteStmt->bind_param('s', $company_client_uuid);

			if ($deleteStmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Cliente da empresa desativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao deletar cliente da empresa: " . $this->mysqli->error]);
			}

			$this->mysqli->close();
		}

		// "PUT /v1/company/client/reactivate/([\w-]+)"
		public function reactivate_company_client($company_client_uuid) {
            validate_token();

			// User exists, proceed with reactivation
			$reactivateQuery = "UPDATE company_client SET deleted_at = NULL WHERE uuid = ?";
			$reactivateStmt = $this->mysqli->prepare($reactivateQuery);
			$reactivateStmt->bind_param('s', $company_client_uuid);

			if ($reactivateStmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Cliente da empresa reativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao reativar cliente da empresa: " . $this->mysqli->error]);
			}

			$this->mysqli->close();
		}

	}