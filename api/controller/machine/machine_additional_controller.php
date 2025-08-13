<?php
	require_once('./service/utils.php');

    // {
    //     "machine_uuid": "bbb401af-7175-11f0-aa98-2a706ee16730",
    //     "additional_uuid": "0d81bd48-73f8-11f0-aa98-2a706ee16730"
    // }
    
	class MachineAdditionalController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		// "GET /v1/machine/additional/([\w-]+)"
		public function get_machine_additional_by_uuid($uuid) {
			validate_token();

			$query = "  SELECT  bma.id AS machine_additional_id, bma.uuid AS machine_additional_uuid, bma.machine_id, bma.additional_id, bma.created_at AS machine_additional_created_at,
                                                bma.deleted_at AS machine_additional_deleted_at, at.id AS additional_type_id, at.uuid AS additional_type_uuid, at.company_id, at.name, at.week_days, 
                                                DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
                                                DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
                                                DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
                                                DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time, '%H:%i') AS end_time, 
                                                at.rate, at.created_at AS additional_type_created_at, at.deleted_at AS additional_type_deleted_at
                                FROM machine_additional bma
                                INNER JOIN additional_type at ON bma.additional_id = at.id
                                WHERE bma.uuid = ? AND bma.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Budget machine additional not found"]);
				exit();
			}

			$machine_additional = $result->fetch_assoc();
			echo json_encode(["machine_additional" => $machine_additional]);
		}

		// "GET /v1/machine/additional/machine/([\w-]+)"
		public function get_machine_additional_by_machine_uuid($machine_uuid) {
			validate_token();

			$query = "  SELECT  bma.id AS machine_additional_id, bma.uuid AS machine_additional_uuid, bma.machine_id, bma.additional_id, bma.created_at AS machine_additional_created_at,
                                                bma.deleted_at AS machine_additional_deleted_at, at.id AS additional_type_id, at.uuid AS additional_type_uuid, at.company_id, at.name, at.week_days, 
                                                DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
                                                DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
                                                DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
                                                DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time, '%H:%i') AS end_time, 
                                                at.rate, at.created_at AS additional_type_created_at, at.deleted_at AS additional_type_deleted_at
                                FROM machine_additional bma
                                INNER JOIN additional_type at ON bma.additional_id = at.id
                                WHERE bma.machine_id = (SELECT id FROM machine WHERE uuid = ?) AND bma.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$items = [];
			while ($row = $result->fetch_assoc()) {
				$items[] = $row;
			}

			echo json_encode(["machine_additionals" => $items]);
		}

		public function get_machine_additional_by_company_uuid($company_uuid) {
			validate_token();
		
			$query_machines = "SELECT m.id AS machine_id,m.uuid AS machine_uuid, m.name AS machine_name,m.brand,m.license_plate, m.serial_number,
																DATE_FORMAT(m.created_at, '%d/%m/%Y') AS machine_created_at
												FROM machine m
												WHERE m.company_id = (SELECT id FROM company WHERE uuid = ?) AND m.deleted_at IS NULL
												ORDER BY m.name";
			$stmt_machines = $this->mysqli->prepare($query_machines);
			$stmt_machines->bind_param('s', $company_uuid);
			$stmt_machines->execute();
			$result_machines = $stmt_machines->get_result();
		
			// Statement reutilizável para adicionais
			$query_additionals = " SELECT bma.uuid AS machine_additional_uuid, at.uuid AS additional_type_uuid, at.name,at.week_days,
																DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
																DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
																DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
																DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time,   '%H:%i') AS end_time, at.rate
													FROM machine_additional bma
													INNER JOIN additional_type at
															ON at.id = bma.additional_id
														AND at.deleted_at IS NULL
													WHERE bma.machine_id = ?
													AND bma.deleted_at IS NULL
													ORDER BY at.name, bma.id";
			$stmt_adds = $this->mysqli->prepare($query_additionals);
		
			$machines = [];
		
			while ($m = $result_machines->fetch_assoc()) {
				$additionals = [];
		
				$mid = (int)$m['machine_id'];
				$stmt_adds->bind_param('i', $mid);
				$stmt_adds->execute();
				$res_adds = $stmt_adds->get_result();
		
				// while dos adicionais da máquina atual
				while ($row = $res_adds->fetch_assoc()) {
		
					$additionals[] = $row;
				}
		
				$machines[] = [
					"machine_id"           => $mid,
					"machine_uuid"         => $m["machine_uuid"],
					"machine_name"         => $m["machine_name"],
					"brand"                => $m["brand"],
					"license_plate"        => $m["license_plate"],
					"serial_number"        => $m["serial_number"],
					"machine_created_at"   => $m["machine_created_at"],
					"additionals"          => $additionals,
				];
			}
		
			echo json_encode([
				"machines" => $machines
			]);
		}
		
		

		// "POST /v1/machine/additional/"
		public function create_machine_additional() {
			validate_token();
			$data = validate_payload(["machine_uuid", "additional_uuid"]);

			$this->mysqli->begin_transaction();

			try {
				$query = "INSERT INTO machine_additional (machine_id, additional_id)
						  VALUES ((SELECT id FROM machine WHERE uuid = ?), (SELECT id FROM additional_type WHERE uuid = ?))";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('ss', $data->machine_uuid, $data->additional_uuid);

				if (!$stmt->execute()) {
					throw new Exception("Erro ao adicionar taxa adicional na máquina.");
				}

				$this->mysqli->commit();

				http_response_code(201);
				echo json_encode(["message" => "Taxa adicional adicionada na máquina com sucesso."]);

			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}

		// "DELETE /v1/machine/additional/([\w-]+)"
		public function delete_machine_additional($machine_additional_uuid) {
			validate_token();

			$query = "UPDATE machine_additional SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_additional_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Taxa adicional da máquina desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar taxa adicional da máquina."]);
			}
		}
	}
