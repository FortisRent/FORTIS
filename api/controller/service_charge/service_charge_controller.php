<?php
	require_once('./service/utils.php');


	class ServiceChargeController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		// "GET /v1/service/charge/([\w-]+)"
		public function get_service_charge_by_uuid($service_charge_uuid) {
			validate_token();

			$query = " SELECT sc.uuid AS service_charge_uuid, sc.name AS service_charge_name, (sc.amount / 100) AS fee_amount, sc.observation
								FROM service_charge sc
								WHERE sc.uuid = ? AND sc.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $service_charge_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Service charge not found"]);
				exit();
			}

			$service_charge = $result->fetch_assoc();
			echo json_encode(["service_charge" => $service_charge]);
		}

		// "GET /v1/service/charge/company/([\w-]+)"
		public function get_service_charge_by_company_uuid($company_uuid) {

           validate_token();

			$query = " SELECT sc.uuid AS service_charge_uuid, sc.name AS service_charge_name, (sc.amount / 100) AS fee_amount, sc.observation
								FROM service_charge sc
								INNER JOIN company c ON sc.company_id = c.id
								WHERE c.uuid = ? AND sc.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$service_charge_company = [];
			while ($row = $result->fetch_assoc()) {
				$row['fee_amount'] = number_format($row['fee_amount'], 2, ',', '.');
				$service_charge_company[] = $row;
			}

			echo json_encode(["service_charge_company" => $service_charge_company]);
		}

        // // "POST /v1/service/charge/"
		public function create_service_charge() {
			validate_token();
			$data = validate_payload(["company_uuid", "name", "amount"]);
		
			$this->mysqli->begin_transaction();
		
			try {
				$query = "INSERT INTO service_charge (company_id, name, amount)
						  VALUES ((SELECT id FROM company WHERE uuid = ?), ?, ?)";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('ssi', $data->company_uuid, $data->name, $data->amount);
		
				if (!$stmt->execute()) {
					throw new Exception("Erro ao adicionar tipo de taxa.");
				}
		
				$this->mysqli->commit();
		
				http_response_code(201);
				echo json_encode(["message" => "Tipo de taxa adicionada com sucesso."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
		
		// "PUT /v1/service/charge/([\w-]+)"
		public function update_service_charge($service_charge_uuid) {
			validate_token();

			$data = validate_payload(["name", "amount"]);

			$query = "UPDATE service_charge SET `name` = ?, amount = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('sis', $data->name, $data->amount, $service_charge_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Tipo de taxa atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar tipo de taxa."]);
			}
		}

		// "DELETE /v1/service/charge/([\w-]+)" 
		public function delete_service_charge($service_charge_uuid) {
			$query = "UPDATE service_charge SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $service_charge_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Tipo de taxa desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar tipo de taxa."]);
			}
		}
		
	}