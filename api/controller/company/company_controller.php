<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class CompanyController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/company/"
		public function get_all_companies() {
			$query = " SELECT c.*, u.full_name as responsible_name, u.profile_picture_url, ca.uuid as address_uuid, ca.zip_code, ca.street, ca.number_street, ca.complement, ca.neighborhood, city.name as city_name, gs.name as state_name
								FROM company c
								LEFT JOIN company_address ca ON ca.company_id = c.id
								LEFT JOIN city city ON ca.city_id = city.id
								LEFT JOIN geo_state gs ON city.state_id = gs.id
                                INNER JOIN user u ON c.responsible_id = u.id
                                WHERE c.deleted_at IS NULL;";

			$result = $this->mysqli->query($query);

			if ($result) {
				$companies = [];
				while ($row = $result->fetch_assoc()) {
					$companies[] = $row;
				}
				echo json_encode(["companies" => $companies]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        // "GET /v1/company/(\w+)"
		public function get_company_by_uuid($company_uuid) {
			$query = "  SELECT c.*, u.full_name as responsible_name, u.profile_picture_url, ca.uuid as address_uuid, ca.zip_code, ca.street, ca.number_street, ca.complement, ca.neighborhood, city.name as city_name, gs.name as state_name
								FROM company c
								LEFT JOIN company_address ca ON ca.company_id = c.id
								LEFT JOIN city city ON ca.city_id = city.id
								LEFT JOIN geo_state gs ON city.state_id = gs.id
                                INNER JOIN user u ON c.responsible_id = u.id
								WHERE c.uuid = ? AND c.deleted_at IS NULL";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "company not found"]);
				exit();
			}

			$company = $result->fetch_assoc();
			echo json_encode(["company" => $company]);
		}

		public function get_all_company_address($company_uuid) {
			$query = " SELECT c.uuid, c.name as company_name, ca.uuid as address_uuid, ca.zip_code, ca.street, ca.number_street, ca.complement, ca.neighborhood, city.name as city_name, gs.name as state_name
								FROM company_address ca
								LEFT JOIN company c ON ca.company_id = c.id
								LEFT JOIN city city ON ca.city_id = city.id
								LEFT JOIN geo_state gs ON city.state_id = gs.id
                                WHERE ca.company_id = (SELECT id FROM company WHERE uuid = ?) AND c.deleted_at IS NULL;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result) {
				$company_addresses = [];
				while ($row = $result->fetch_assoc()) {
					$company_addresses[] = $row;
				}
				echo json_encode(["company_addresses" => $company_addresses]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        //  "GET /v1/company/logged/
		public function get_company_by_logged() {

            $token = validate_token();

			$query = "  SELECT c.*, u.full_name as responsible_name, u.profile_picture_url, ca.uuid as address_uuid, ca.zip_code, ca.street, ca.number_street, ca.complement, ca.neighborhood, city.name as city_name, gs.name as state_name
								FROM company c
								LEFT JOIN company_address ca ON ca.company_id = c.id
								LEFT JOIN city city ON ca.city_id = city.id
								LEFT JOIN geo_state gs ON city.state_id = gs.id
								INNER JOIN user u ON c.responsible_id = u.id
								WHERE u.id = (SELECT id FROM user WHERE uuid = ?) AND c.deleted_at IS NULL ";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$company = [];
			while ($row = $result->fetch_assoc()) {
				$company[] = $row;
			}

			echo json_encode(["company" => $company]);
		}

        // "POST /v1/company/"
		public function create_company() {
            $token = validate_token();
        
            $data = validate_payload(["name", "cnpj", "city_name", "zip_code", "street", "number_street", "complement", "neighborhood"]);
        
            $this->mysqli->begin_transaction();
        
            try {
                $identifier = generate_identifier(16);
                $company_uuid = generate_uuid_v3(64);
                $query_company = "INSERT INTO company (uuid, name, cnpj, responsible_id) VALUES (?, ?, ?, (SELECT id FROM user WHERE uuid = ?))";
                $stmt_company = $this->mysqli->prepare($query_company);

                $stmt_company->bind_param('ssss', $company_uuid, $data->name, $data->cnpj, $token->uuid);

                $stmt_company->execute();
        
                $company_id = $this->mysqli->insert_id;
        
                $query_company_address = "INSERT INTO company_address (company_id, city_id, zip_code, street, number_street, complement, neighborhood) VALUES (?, (SELECT id FROM city WHERE name = ?), ?, ?, ?, ?, ?)";
                $stmt_company_address = $this->mysqli->prepare($query_company_address);
                $company_address_uuid = generate_uuid_v3(64);
                $stmt_company_address->bind_param('isissss',$company_id, $data->city_name, $data->zip_code, $data->street, $data->number_street, $data->complement, $data->neighborhood);
                $stmt_company_address->execute();
        
                $this->mysqli->commit();
        
                http_response_code(201);
                echo json_encode([
                    "message" => "Empresa e endereço da empresa cadastrados com sucesso."
                ]);
            } catch (Exception $e) {
                $this->mysqli->rollback();
                http_response_code(500);
                echo json_encode(["message" => "Erro ao cadastrar empresa: " . $e->getMessage()]);
            }
        }

		public function update_company($company_uuid) {
			validate_token();
		
			$data = validate_payload(["name", "cnpj", "city_name", "zip_code", "street", "number_street", "complement", "neighborhood"]);
		
			try {
				$this->mysqli->begin_transaction();
		
				$query_company = "UPDATE company SET name = ?, cnpj = ? WHERE uuid = ?";
				$stmt_company = $this->mysqli->prepare($query_company);
				$stmt_company->bind_param('sss', $data->name, $data->cnpj, $company_uuid);
				if (!$stmt_company->execute()) {
					throw new Exception("Erro ao atualizar empresa.");
				}
		
				$query_address = "UPDATE company_address SET city_id = (SELECT id FROM city WHERE name = ?), zip_code = ?, street = ?, number_street = ?, complement = ?, neighborhood = ? WHERE company_id = (SELECT id FROM company WHERE uuid = ?)";
				$stmt_address = $this->mysqli->prepare($query_address);
				$stmt_address->bind_param('sisssss', $data->city_name, $data->zip_code, $data->street, $data->number_street, $data->complement, $data->neighborhood, $company_uuid);
				if (!$stmt_address->execute()) {
					throw new Exception("Erro ao atualizar endereço da empresa.");
				}
		
				$this->mysqli->commit();
				http_response_code(200);
				echo json_encode(["message" => "Empresa e endereço atualizados com sucesso."]);
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}


        // "DELETE /v1/company/(\w+)"
		public function delete_company($company_uuid) {
			$query = "UPDATE company SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Empresa desativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desativar empresa."]);
			}
		}

          // "PUT /v1/company/reactivate/(\w+)"
		public function reactivate_company($company_uuid) {
			$query = "UPDATE company SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Empresa reativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar empresa."]);
			}
		}

	}