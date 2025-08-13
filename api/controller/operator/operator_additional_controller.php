<?php
	require_once('./service/utils.php');

    // {
    //     "operator_uuid": "bbb401af-7175-11f0-aa98-2a706ee16730",
    //     "additional_uuid": "0d81bd48-73f8-11f0-aa98-2a706ee16730"
    // }

	class OperatorAdditionalController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		// "GET /v1/operator/additional/([\w-]+)"
		public function get_operator_additional_by_uuid($uuid) {
			validate_token();

			$query = "  SELECT  bmoa.id AS operator_additional_id, bmoa.uuid AS operator_additional_uuid, bmoa.operator_id, bmoa.additional_id, bmoa.created_at AS operator_additional_created_at,
                                                bmoa.deleted_at AS operator_additional_deleted_at, at.id AS additional_type_id, at.uuid AS additional_type_uuid, at.company_id, at.name, at.week_days, 
                                                DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
                                                DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
                                                DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
                                                DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time, '%H:%i') AS end_time, 
                                                at.rate, at.created_at AS additional_type_created_at, at.deleted_at AS additional_type_deleted_at
                                FROM operator_additional bmoa
                                INNER JOIN additional_type at ON bmoa.additional_id = at.id
                                WHERE bmoa.uuid = ? AND bmoa.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Budget machine operator additional not found"]);
				exit();
			}

			$operator_additional = $result->fetch_assoc();
			echo json_encode(["operator_additional" => $operator_additional]);
		}

		// "GET /v1/operator/additional/operator/([\w-]+)"
		public function get_operator_additional_by_operator_uuid($operator_uuid) {
			validate_token();

			$query = "  SELECT  bmoa.id AS operator_additional_id, bmoa.uuid AS operator_additional_uuid, bmoa.operator_id, bmoa.additional_id, bmoa.created_at AS operator_additional_created_at,
                                                bmoa.deleted_at AS operator_additional_deleted_at, at.id AS additional_type_id, at.uuid AS additional_type_uuid, at.company_id, at.name, at.week_days, 
                                                DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
                                                DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
                                                DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
                                                DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time, '%H:%i') AS end_time, 
                                                at.rate, at.created_at AS additional_type_created_at, at.deleted_at AS additional_type_deleted_at
                                FROM operator_additional bmoa
                                INNER JOIN additional_type at ON bmoa.additional_id = at.id
                                WHERE bmoa.operator_id = (SELECT id FROM operator WHERE uuid = ?) AND bmoa.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $operator_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$items = [];
			while ($row = $result->fetch_assoc()) {
				$items[] = $row;
			}

			echo json_encode(["operator_additionals" => $items]);
		}

		// "GET /v1/operator/additional/company/([\w-]+)"
		public function get_operator_additional_by_company_uuid($company_uuid) {
			validate_token();
		
			$query_operators = "SELECT e.id AS operator_id, e.uuid AS operator_uuid, DATE_FORMAT(e.created_at, '%d/%m/%Y') AS operator_created_at,
																u.full_name AS employee_name, u.email, u.phone, r.name AS role_name
												FROM employee e
												INNER JOIN `user` u ON u.id = e.user_id
												LEFT JOIN `role` r ON r.id = e.role_id
												WHERE e.company_id = (SELECT id FROM company WHERE uuid = ?) AND e.deleted_at IS NULL
												ORDER BY u.full_name";
			$stmt_ops = $this->mysqli->prepare($query_operators);
			$stmt_ops->bind_param('s', $company_uuid);
			$stmt_ops->execute();
			$result_ops = $stmt_ops->get_result();
		
			$query_additionals = "	SELECT oa.uuid AS operator_additional_uuid, at.uuid AS additional_type_uuid,at.name,at.week_days,
																DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
																DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
																DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date,DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
																DATE_FORMAT(at.start_time, '%H:%i') AS start_time,DATE_FORMAT(at.end_time,   '%H:%i') AS end_time, at.rate
													FROM operator_additional oa
													INNER JOIN additional_type at
															ON at.id = oa.additional_id
														AND at.deleted_at IS NULL
													WHERE oa.operator_id = ?
													AND oa.deleted_at IS NULL
													ORDER BY at.name, oa.id";
			$stmt_adds = $this->mysqli->prepare($query_additionals);
		
			$operators = [];
		
			while ($op = $result_ops->fetch_assoc()) {
				$additionals = [];
		
				$oid = (int)$op['operator_id'];
				$stmt_adds->bind_param('i', $oid);
				$stmt_adds->execute();
				$res_adds = $stmt_adds->get_result();
		
				while ($row = $res_adds->fetch_assoc()) {
					$additionals[] = $row;
				}
		
				$operators[] = [
					"operator_id"          => $oid,
					"operator_uuid"        => $op["operator_uuid"],
					"operator_created_at"  => $op["operator_created_at"],
					"employee_name"        => $op["employee_name"],
					"role_name"            => $op["role_name"],
					"email"                => $op["email"],
					"phone"                => $op["phone"],
					"additionals"          => $additionals,
				];
			}
		
			echo json_encode([
				"operators" => $operators
			]);
		}
		

		// "POST /v1/operator/additional/"
		public function create_operator_additional() {
			validate_token();
			$data = validate_payload(["operator_uuid", "additional_uuid"]);

			$this->mysqli->begin_transaction();

			try {
				$query = "INSERT INTO operator_additional (operator_id, additional_id)
						  VALUES ((SELECT id FROM employee WHERE uuid = ?), (SELECT id FROM additional_type WHERE uuid = ?))";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('ss', $data->operator_uuid, $data->additional_uuid);

				if (!$stmt->execute()) {
					throw new Exception("Erro ao adicionar taxa adicional no operador da máquina.");
				}

				$this->mysqli->commit();

				http_response_code(201);
				echo json_encode(["message" => "Taxa adicional no operador da máquina adicionada com sucesso."]);

			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}

		// "DELETE /v1/operator/additional/([\w-]+)"
		public function delete_operator_additional($operator_additional_uuid) {
			validate_token();

			$query = "UPDATE operator_additional SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $operator_additional_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Taxa adicional no operador da máquina desativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar taxa adicional no operador da máquina."]);
			}
		}
	}
