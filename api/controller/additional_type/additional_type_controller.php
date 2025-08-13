<?php
	require_once('./service/utils.php');

	// {
	// 	"company_uuid": "fvKNndUis2tTeuxcvAEi3wMXFimGnH4q9TXp0F4cqyW9p5bdgRJxoPyJJlbGQERj",
	// 	"name": "Adicional Noturno",
	// 	"week_days": "1,2,3,4,5",
	// 	"holiday_date": null,
	// 	"start_date": null,
	// 	"end_date": null,
	// 	"start_time": "22:00:00",
	// 	"end_time": "05:00:00",
	// 	"rate": 20
	//   }

	class AdditionalTypeController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		// "GET /v1/additional/type/([\w-]+)"
		public function get_additional_type_by_uuid($additional_type_uuid) {
			validate_token();

			$query = " SELECT 	at.id, at.uuid, at.company_id, at.name, at.week_days, 
												DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
												DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
												DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
												DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time, '%H:%i') AS end_time, at.rate, at.created_at, at.deleted_at
								FROM additional_type at
								WHERE at.uuid = ? AND at.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $additional_type_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Additional type not found"]);
				exit();
			}

			$additional_type = $result->fetch_assoc();
			echo json_encode(["additional_type" => $additional_type]);
		}

		// "GET /v1/additional/type/company/([\w-]+)"
		public function get_additional_type_by_company_uuid($company_uuid) {

           validate_token();

			$query = " SELECT 	at.id, at.uuid, at.company_id, at.name, at.week_days, 
												DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
												DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
												DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
												DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time, '%H:%i') AS end_time, at.rate, at.created_at, at.deleted_at
								FROM additional_type at
								WHERE at.company_id = (SELECT id FROM company WHERE uuid = ?) AND at.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$additional_type_company = [];
			while ($row = $result->fetch_assoc()) {
				$additional_type_company[] = $row;
			}

			echo json_encode(["additional_type_company" => $additional_type_company]);
		}

        // // "POST /v1/additional/type/"
		public function create_additional_type() {
			validate_token();
			$data = validate_payload(["company_uuid", "name", "rate"]);
		
			$this->mysqli->begin_transaction();
		
			try {
				$query = "INSERT INTO additional_type (company_id, name, week_days, holiday_date, start_date, end_date, start_time, end_time, rate)
						  VALUES ((SELECT id FROM company WHERE uuid = ?), ?, ?, ?, ?, ?, ?, ?, ?)";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('ssssssssi', $data->company_uuid, $data->name, $data->week_days, $data->holiday_date, $data->start_date, $data->end_date, $data->start_time, $data->end_time, $data->rate);
		
				if (!$stmt->execute()) {
					throw new Exception("Erro ao adicionar tipo de adicional.");
				}
		
				$this->mysqli->commit();
		
				http_response_code(201);
				echo json_encode(["message" => "Tipo de adicional adicionado com sucesso."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
		
		// "PUT /v1/additional/type/([\w-]+)"
		public function update_additional_type($additional_type_uuid) {
			validate_token();

			$data = validate_payload(["name", "week_days", "holiday_date"]);

			$query = "UPDATE additional_type SET `name` = ?, week_days = ?, holiday_date = ?, start_date = ?, end_date = ?, start_time = ?, end_time = ?, rate = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('sisssssis', $data->name, $data->week_days, $data->holiday_date, $data->start_date, $data->end_date,$data->start_time, $data->end_time, $data->rate, $additional_type_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Tipo de adicional atualizado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar tipo de adicional."]);
			}
		}

		// "DELETE /v1/additional/type/([\w-]+)" 
		public function delete_additional_type($additional_type_uuid) {
			$query = "UPDATE additional_type SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $additional_type_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Tipo de adicional desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar tipo de adicional."]);
			}
		}
		
	}