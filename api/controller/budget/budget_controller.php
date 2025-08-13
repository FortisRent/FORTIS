<?php
	require_once('./service/utils.php');

	// {
	// 	"project_uuid": "KkjU8In3ioocxeoAzWtYtpu0DddyhoCHKB8iRrMZjhAfs7pBxbxvtEjke03nkd8s",
	// 	"machine_list":[
	// 		{ "machine_uuid": "wYzSh0ZVGuypNGASsoAinjqUvUP1U5srWJb7rEnLhKMeu1hVieGrjNoqzYJmSkIt", "company_id": 28 },
	// 		{ "machine_uuid": "Va9653EV8HR0MdkbTlOzKQ8Eha2t7pokfQOZWrsejZA4dfNSSQu1pPHIM5FqaQWB", "company_id": 21 },
	// 		{ "machine_uuid": "oRbbeCOn7iiQdnwohvlUdSl5yUWtZ6dhzwxtosHmhNquc71x5tJWHGZacjtC96ox", "company_id": 28 }
	// 	]
	// }
	

	class BudgetController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/budget/(\w+)"
		public function get_budget_by_uuid($budget_uuid) {
			validate_token();

			$query_budget = "SELECT b.uuid as budget_uuid, p.uuid as project_uuid, b.name as budget_name, p.name as project_name, p.description as project_description, (b.amount / 100) AS amount, (b.total_fee / 100) AS total_fee, b.total_distance, b.condition, 
															b.observation AS budget_observation, b.total_distance, DATE_FORMAT(b.expected_date, '%d/%m/%Y') AS expected_date, DATE_FORMAT(b.expected_date, '%H:%i') AS expected_hour, DATE_FORMAT(b.expected_date_end, '%d/%m/%Y') AS expected_date_end, DATE_FORMAT(b.expected_date_end, '%H:%i') AS expected_hour_end,
															 s.name as status_name, u.full_name as client_name, c.name AS client_company_name, p.payer_name, p.cnpj, DATE_FORMAT(b.created_at, '%d/%m/%Y') as created_at
											FROM budget b
											INNER JOIN project p ON b.project_id = p.id
											LEFT JOIN project_client pc ON pc.project_id = p.id
											LEFT JOIN client c ON pc.client_id = c.id
											LEFT JOIN project_user pu ON pu.project_id = p.id
											LEFT JOIN `user` u ON pu.user_id = u.id
											INNER JOIN budget_history bh ON bh.id = (
													SELECT bh1.id 
													FROM budget_history bh1 
													WHERE bh1.budget_id = b.id 
													ORDER BY bh1.created_at DESC 
													LIMIT 1
												)
											INNER JOIN status s ON bh.status_id = s.id
											WHERE b.uuid = ? AND b.deleted_at IS NULL;";
		
			$stmt_budget = $this->mysqli->prepare($query_budget);
			$stmt_budget->bind_param('s', $budget_uuid);
			$stmt_budget->execute();
			$result_budget = $stmt_budget->get_result();
			$budget_data = $result_budget->fetch_assoc();

			$budget_data['total_fee'] =  isset($budget_data['total_fee']) ? number_format($budget_data['total_fee'], 2, ',', '.') : null;
			$budget_data['amount'] =  isset($budget_data['amount']) ? number_format($budget_data['amount'], 2, ',', '.') : null;

			$query_budget_charge = "SELECT b.uuid as budget_uuid, sc.name AS charge_name, (bsc.fee_amount / 100) AS fee_amount, sc.observation
															FROM budget_service_charge bsc
															INNER JOIN budget b ON bsc.budget_id = b.id
															INNER JOIN service_charge sc ON bsc.service_charge_id = sc.id
															WHERE b.uuid = ? AND b.deleted_at IS NULL;";
		
			$stmt_budget_charge = $this->mysqli->prepare($query_budget_charge);
			$stmt_budget_charge->bind_param('s', $budget_uuid);
			$stmt_budget_charge->execute();
			$result_budget_charge = $stmt_budget_charge->get_result();

			$budget_charge_data = [];

			while ($row = $result_budget_charge->fetch_assoc()) {
				$row['fee_amount'] = number_format($row['fee_amount'], 2, ',', '.');

				$budget_charge_data[] = $row;
			}
			
			$query_budget_machine = "SELECT bm.id AS budget_machine_id,bm.uuid AS budget_machine_uuid, b.uuid AS budget_uuid,m.name AS machine_name, c.name AS company_name, mc.name AS category_name, 
																		m.parameters, m.max_volume, m.max_weight, m.max_height, m.max_radius, m.year_manufacture, m.brand, m.license_plate, m.serial_number, m.jib, m.chassis_number, m.crane_truck_data, 
																		m.trailer_data,(m.price / 100) AS price, (mf.price_per_hour / 100) AS price_per_hour, mf.minimum_rental_period,(mf.price_per_distance / 100) AS price_per_distance,mf.distance_amount, 
																		mf.special_hour_fee,mf.observation, DATE_FORMAT(b.created_at, '%d/%m/%Y') AS buget_created_at, COALESCE((bm.price_per_hour / 100), 0) AS budget_machine_price_per_hour, 
																		COALESCE((bm.price_per_distance / 100), 0) AS budget_machine_price_per_distance, bm.minimum_rental_period AS budget_machine_minimum_rental_period
															FROM budget_machine bm
															INNER JOIN budget b ON bm.budget_id = b.id
															INNER JOIN machine m ON bm.machine_id = m.id
															INNER JOIN machine_category mc ON m.category_id = mc.id
															LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
															LEFT JOIN company c ON b.company_id = c.id
															WHERE b.uuid = ? AND bm.deleted_at IS NULL;";
		
			$stmt_budget_machine = $this->mysqli->prepare($query_budget_machine);
			$stmt_budget_machine->bind_param('s', $budget_uuid);
			$stmt_budget_machine->execute();
			$result_budget_machine = $stmt_budget_machine->get_result();
		
			$machines = [];
			$minimum_rental_price_total = 0;

			while ($row = $result_budget_machine->fetch_assoc()) {
				$row['parameters'] = cleanJsonData($row['parameters']);
				$row['crane_truck_data'] = cleanJsonData($row['crane_truck_data']);
				$row['trailer_data'] = cleanJsonData($row['trailer_data']);
				$row['employees'] = [];

				$machine_price = $row['price_per_hour'] * $row['minimum_rental_period'];
				$minimum_rental_price_total += $machine_price;

				// Buscar operadores
				$query_employees = "SELECT bmo.uuid AS machine_operator_uuid, e.uuid AS employee_uuid, op.full_name AS operator_name, r.name AS role_name
														FROM budget_machine_operator bmo
														INNER JOIN role r ON bmo.role_id = r.id
														LEFT JOIN employee e ON bmo.employee_id = e.id
														LEFT JOIN user op ON e.user_id = op.id
														WHERE bmo.budget_machine_id = ?";
				$stmt_employees = $this->mysqli->prepare($query_employees);
				$stmt_employees->bind_param('i', $row['budget_machine_id']);
				$stmt_employees->execute();
				$result_employees = $stmt_employees->get_result();

				while ($emp = $result_employees->fetch_assoc()) {
					$row['employees'][] = $emp;
				}

				$machines[] = [
					'budget_machine_uuid'   => $row['budget_machine_uuid'],
					'machine_name'          => $row['machine_name'],
					'company_name'          => $row['company_name'],
					'category_name'         => $row['category_name'],
					'max_volume'            => $row['max_volume'],
					'max_weight'            => $row['max_weight'],
					'max_height'            => $row['max_height'],
					'max_radius'            => $row['max_radius'],
					'parameters'            => $row['parameters'],
					'crane_truck_data'      => $row['crane_truck_data'],
					'trailer_data'          => $row['trailer_data'],
					'year_manufacture'      => $row['year_manufacture'],
					'brand'                 => $row['brand'],
					'license_plate'         => $row['license_plate'],
					'serial_number'         => $row['serial_number'],
					'chassis_number'        => $row['chassis_number'],
					'jib'                   => $row['jib'],
					'price'                 => $row['price'] !== null ? number_format($row['price'], 2, ',', '.') : null,
					'price_per_hour'        => $row['price_per_hour'] !== null ? number_format($row['price_per_hour'], 2, ',', '.') : null,
					'minimum_rental_period' => $row['minimum_rental_period'],
					'price_per_distance'    => $row['price_per_distance'] !== null ? number_format($row['price_per_distance'], 2, ',', '.') : null,
					'distance_amount'       => $row['distance_amount'] !== null ? number_format($row['distance_amount'], 2, ',', '.') : null,
					'budget_machine_price_per_distance' => $row['budget_machine_price_per_distance'] !== null ? number_format($row['budget_machine_price_per_distance'], 2, ',', '.') : null,
					'budget_machine_price_per_hour'     => $row['budget_machine_price_per_hour'] !== null ? number_format($row['budget_machine_price_per_hour'], 2, ',', '.') : null,
					'budget_machine_minimum_rental_period' => $row['budget_machine_minimum_rental_period'],
					'special_hour_fee'      => $row['special_hour_fee'],
					'observation'           => $row['observation'],
					'employees'             => $row['employees'],
				];				
			}

			$minimum_rental_price = number_format($minimum_rental_price_total, 2, ',', '.');
			$budget_data['minimum_rental_price'] = $minimum_rental_price;
		
			echo json_encode([
				"budget" => $budget_data,
				"budget_charge"=> $budget_charge_data,
				"budget_machine" => $machines,
				"minimum_rental_price" => $minimum_rental_price,
				"total_fee" => $budget_data['total_fee'],
				"amount" => $budget_data['amount'],
			]);
		}

		public function get_budget_details_by_uuid($budget_uuid) {
			validate_token();

			$query_budget_company = " SELECT c.name, c.cnpj
															FROM budget b
															LEFT JOIN company c ON b.company_id = c.id
															WHERE b.id = (SELECT id FROM budget WHERE uuid = ?) 
															AND b.deleted_at IS NULL;";
		
			$stmt_budget_company = $this->mysqli->prepare($query_budget_company);
			$stmt_budget_company->bind_param('s', $budget_uuid);
			$stmt_budget_company->execute();
			$result_budget_company = $stmt_budget_company->get_result();
			$budget_company_data = $result_budget_company->fetch_assoc();

			$query_budget = "SELECT b.uuid as budget_uuid, p.uuid as project_uuid, b.name as budget_name, p.name as project_name, p.description as project_description, (b.amount / 100) AS amount, (b.total_fee / 100) AS total_fee, b.total_distance, b.condition, 
															b.observation AS budget_observation, b.total_distance, DATE_FORMAT(b.expected_date, '%d/%m/%Y') AS expected_date, DATE_FORMAT(b.expected_date, '%H:%i') AS expected_hour, DATE_FORMAT(b.expected_date_end, '%d/%m/%Y') AS expected_date_end, DATE_FORMAT(b.expected_date_end, '%H:%i') AS expected_hour_end,
															 s.name as status_name, mc.name AS name_machine_category, u.full_name as client_name, c.name AS client_company_name, p.payer_name, p.cnpj, pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, city.name AS city_name, gs.name AS state_name
											FROM budget b
											INNER JOIN budget_machine bm ON bm.budget_id = bm.id
											INNER JOIN project p ON b.project_id = p.id
											LEFT JOIN project_client pc ON pc.project_id = p.id
											LEFT JOIN client c ON pc.client_id = c.id
											LEFT JOIN project_user pu ON pu.project_id = p.id
											LEFT JOIN `user` u ON pu.user_id = u.id
											INNER JOIN project_address pa ON pa.project_id = p.id
											INNER JOIN city city ON pa.city_id = city.id
											INNER JOIN geo_state gs ON city.state_id = gs.id
											LEFT JOIN project_stage ps ON ps.project_id = p.id
											INNER JOIN machine_category mc ON ps.machine_category_id = mc.id
											INNER JOIN machine m ON bm.machine_id = m.id
											INNER JOIN budget_history bh ON bh.id = (
													SELECT bh1.id 
													FROM budget_history bh1 
													WHERE bh1.budget_id = b.id 
													ORDER BY bh1.created_at DESC 
													LIMIT 1
												)
											INNER JOIN status s ON bh.status_id = s.id
											WHERE b.uuid = ? AND b.deleted_at IS NULL;";
		
			$stmt_budget = $this->mysqli->prepare($query_budget);
			$stmt_budget->bind_param('s', $budget_uuid);
			$stmt_budget->execute();
			$result_budget = $stmt_budget->get_result();
			$budget_data = $result_budget->fetch_assoc();

			$budget_data['total_fee'] =  isset($budget_data['total_fee']) ? number_format($budget_data['total_fee'], 2, ',', '.') : null;
			$budget_data['amount'] =  isset($budget_data['amount']) ? number_format($budget_data['amount'], 2, ',', '.') : null;

			$query_budget_charge = "SELECT b.uuid as budget_uuid, sc.name AS charge_name, (bsc.fee_amount / 100) AS fee_amount, sc.observation
															FROM budget_service_charge bsc
															INNER JOIN budget b ON bsc.budget_id = b.id
															INNER JOIN service_charge sc ON bsc.service_charge_id = sc.id
															WHERE b.uuid = ? AND b.deleted_at IS NULL;";
		
			$stmt_budget_charge = $this->mysqli->prepare($query_budget_charge);
			$stmt_budget_charge->bind_param('s', $budget_uuid);
			$stmt_budget_charge->execute();
			$result_budget_charge = $stmt_budget_charge->get_result();

			$budget_charge_data = [];

			while ($row = $result_budget_charge->fetch_assoc()) {
				$row['fee_amount'] = number_format($row['fee_amount'], 2, ',', '.');

				$budget_charge_data[] = $row;
			}
			
			$query_budget_machine = "SELECT bm.id AS budget_machine_id,bm.uuid AS budget_machine_uuid, b.uuid AS budget_uuid,m.name AS machine_name, c.name AS company_name, mc.name AS category_name, 
																		m.parameters, m.max_volume, m.max_weight, m.max_height, m.max_radius, m.year_manufacture, m.brand, m.license_plate, m.serial_number, m.jib, m.chassis_number, m.crane_truck_data, 
																		m.trailer_data,(m.price / 100) AS price, (mf.price_per_hour / 100) AS price_per_hour, mf.minimum_rental_period,(mf.price_per_distance / 100) AS price_per_distance,mf.distance_amount, 
																		mf.special_hour_fee,mf.observation, DATE_FORMAT(b.created_at, '%d/%m/%Y') AS buget_created_at, COALESCE((bm.price_per_hour / 100), 0) AS budget_machine_price_per_hour, 
																		COALESCE((bm.price_per_distance / 100), 0) AS budget_machine_price_per_distance, bm.minimum_rental_period AS budget_machine_minimum_rental_period
															FROM budget_machine bm
															INNER JOIN budget b ON bm.budget_id = b.id
															INNER JOIN machine m ON bm.machine_id = m.id
															INNER JOIN machine_category mc ON m.category_id = mc.id
															LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
															LEFT JOIN company c ON b.company_id = c.id
															WHERE b.uuid = ? AND bm.deleted_at IS NULL;";
		
			$stmt_budget_machine = $this->mysqli->prepare($query_budget_machine);
			$stmt_budget_machine->bind_param('s', $budget_uuid);
			$stmt_budget_machine->execute();
			$result_budget_machine = $stmt_budget_machine->get_result();
		
			$machines = [];
			$minimum_rental_price_total = 0;

			while ($row = $result_budget_machine->fetch_assoc()) {
				$row['parameters'] = cleanJsonData($row['parameters']);
				$row['crane_truck_data'] = cleanJsonData($row['crane_truck_data']);
				$row['trailer_data'] = cleanJsonData($row['trailer_data']);
				$row['employees'] = [];

				$machine_price = $row['price_per_hour'] * $row['minimum_rental_period'];
				$minimum_rental_price_total += $machine_price;

				// Buscar operadores
				$query_employees = "SELECT bmo.uuid AS machine_operator_uuid, e.uuid AS employee_uuid, op.full_name AS operator_name, r.name AS role_name
														FROM budget_machine_operator bmo
														INNER JOIN role r ON bmo.role_id = r.id
														LEFT JOIN employee e ON bmo.employee_id = e.id
														LEFT JOIN user op ON e.user_id = op.id
														WHERE bmo.budget_machine_id = ?";
				$stmt_employees = $this->mysqli->prepare($query_employees);
				$stmt_employees->bind_param('i', $row['budget_machine_id']);
				$stmt_employees->execute();
				$result_employees = $stmt_employees->get_result();

				while ($emp = $result_employees->fetch_assoc()) {
					$row['employees'][] = $emp;
				}

				$machines[] = [
					'budget_machine_uuid'   => $row['budget_machine_uuid'],
					'machine_name'          => $row['machine_name'],
					'company_name'          => $row['company_name'],
					'category_name'         => $row['category_name'],
					'max_volume'            => $row['max_volume'],
					'max_weight'            => $row['max_weight'],
					'max_height'            => $row['max_height'],
					'max_radius'            => $row['max_radius'],
					'parameters'            => $row['parameters'],
					'crane_truck_data'      => $row['crane_truck_data'],
					'trailer_data'          => $row['trailer_data'],
					'year_manufacture'      => $row['year_manufacture'],
					'brand'                 => $row['brand'],
					'license_plate'         => $row['license_plate'],
					'serial_number'         => $row['serial_number'],
					'chassis_number'        => $row['chassis_number'],
					'jib'                   => $row['jib'],
					'price'                 => $row['price'] !== null ? number_format($row['price'], 2, ',', '.') : null,
					'price_per_hour'        => $row['price_per_hour'] !== null ? number_format($row['price_per_hour'], 2, ',', '.') : null,
					'minimum_rental_period' => $row['minimum_rental_period'],
					'price_per_distance'    => $row['price_per_distance'] !== null ? number_format($row['price_per_distance'], 2, ',', '.') : null,
					'budget_machine_price_per_distance' => $row['budget_machine_price_per_distance'] !== null ? number_format($row['budget_machine_price_per_distance'], 2, ',', '.') : null,
					'budget_machine_price_per_hour'     => $row['budget_machine_price_per_hour'] !== null ? number_format($row['budget_machine_price_per_hour'], 2, ',', '.') : null,
					'budget_machine_minimum_rental_period' => $row['budget_machine_minimum_rental_period'],
					'distance_amount'       => $row['distance_amount'] !== null ? number_format($row['distance_amount'], 2, ',', '.') : null,
					'special_hour_fee'      => $row['special_hour_fee'],
					'observation'           => $row['observation'],
					'employees'             => $row['employees'],
				];				
			}

			$minimum_rental_price = number_format($minimum_rental_price_total, 2, ',', '.');
			$budget_data['minimum_rental_price'] = $minimum_rental_price;
		
			echo json_encode([
				"budget" => $budget_data,
				"budget_charge"=> $budget_charge_data,
				"budget_machine" => $machines,
				"minimum_rental_price" => $minimum_rental_price,
				"total_fee" => $budget_data['total_fee'],
				"amount" => $budget_data['amount'],
				"budget_company_data" => $budget_company_data,
			]);
		}
        //  "GET /v1/budget/project/(\w+)
		public function get_budget_by_project_uuid($project_uuid) {

            validate_token();

			$query = "		SELECT 	b.uuid AS budget_uuid, b.observation AS budget_observation, (b.amount / 100) AS amount, (b.total_fee/100) AS total_fee, b.condition, b.total_distance, DATE_FORMAT(b.created_at, '%d/%m/%Y') AS created_at, 
													b.uuid AS budget_uuid, GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ' / ') AS machine_name,c.name AS company_name, c.uuid AS company_uuid, s.name AS status_name, p.payer_name, p.cnpj
									FROM budget b
									INNER JOIN budget_machine bm ON bm.budget_id = b.id
									INNER JOIN project p ON b.project_id = p.id
									INNER JOIN machine m ON bm.machine_id = m.id
									INNER JOIN company c ON m.company_id = c.id
									INNER JOIN budget_history bh ON bh.id = (
										SELECT bh1.id 
										FROM budget_history bh1 
										WHERE bh1.budget_id = b.id 
										ORDER BY bh1.created_at DESC 
										LIMIT 1
									)
									INNER JOIN status s ON bh.status_id = s.id
									WHERE p.id = (SELECT id FROM project WHERE uuid = ?)
									AND b.deleted_at IS NULL 
									GROUP BY 
										b.id, b.uuid, b.observation, b.amount, 
										b.condition, b.created_at, b.uuid, c.id, c.name, s.name;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$budget = [];
			while ($row = $result->fetch_assoc()) {
				$row['amount'] = number_format($row['amount'], 2, ',', '.');
				$row['total_fee'] = number_format($row['total_fee'], 2, ',', '.');
				$budget[] = $row;
			}

			echo json_encode(["budget" => $budget]);
		}

		public function get_budget_financial_by_project_uuid($project_uuid) {

            validate_token();

			$query_budget = " SELECT b.uuid as budget_uuid, p.uuid as project_uuid, p.name as project_name, p.payer_name, p.cnpj AS payer_cnpj, s.name as status_name, DATE_FORMAT(b.expected_date, '%d/%m/%Y') as expected_date,
														u.full_name as user_name, u.email, u.phone, c.name AS client_name, c.phone AS client_phone, c.email AS client_email, (bp.discount / 100) AS discount, bp.discount_description, (bp.extra / 100) AS extra, 
														bp.extra_description, b.total_distance, (bp.total_amount/100) AS total_amount
											FROM budget b
											INNER JOIN project p ON b.project_id = p.id
											LEFT JOIN budget_payment bp ON bp.budget_id = b.id
											LEFT JOIN project_client pc ON pc.project_id = p.id
											LEFT JOIN client c ON pc.client_id = c.id
											LEFT JOIN project_user pu ON pu.project_id = p.id
											LEFT JOIN `user` u ON pu.user_id = u.id
											INNER JOIN budget_history bh ON bh.id = (
												SELECT bh1.id 
												FROM budget_history bh1 
												WHERE bh1.budget_id = b.id 
												ORDER BY bh1.created_at DESC 
												LIMIT 1
											)
											INNER JOIN status s ON bh.status_id = s.id
											WHERE b.project_id = (SELECT id FROM project WHERE uuid = ?) 
											AND b.deleted_at IS NULL;";
		
			$stmt_budget = $this->mysqli->prepare($query_budget);
			$stmt_budget->bind_param('s', $project_uuid);
			$stmt_budget->execute();
			$result_budget = $stmt_budget->get_result();
			$budget_data = $result_budget->fetch_assoc();

			$budget_data['discount'] = isset($budget_data['discount']) ? number_format($budget_data['discount'], 2, ',', '.') : null;
			$budget_data['extra'] = isset($budget_data['extra']) ? number_format($budget_data['extra'], 2, ',', '.') : null;
			$budget_data['total_amount'] = isset($budget_data['total_amount']) ? number_format($budget_data['total_amount'], 2, ',', '.') : null;

			$query_budget_charge = "SELECT b.uuid as budget_uuid, sc.name AS charge_name, (bsc.fee_amount / 100) AS fee_amount, sc.observation
															FROM budget_service_charge bsc
															INNER JOIN budget b ON bsc.budget_id = b.id
															INNER JOIN service_charge sc ON bsc.service_charge_id = sc.id
															WHERE b.project_id = ( SELECT id FROM project WHERE uuid = ?) AND b.deleted_at IS NULL;";
		
			$stmt_budget_charge = $this->mysqli->prepare($query_budget_charge);
			$stmt_budget_charge->bind_param('s', $project_uuid);
			$stmt_budget_charge->execute();
			$result_budget_charge = $stmt_budget_charge->get_result();
			$budget_charge_data = $result_budget_charge->fetch_assoc();

			$budget_charge_data = [];

			while ($row = $result_budget_charge->fetch_assoc()) {
				$row['fee_amount'] = number_format($row['fee_amount'], 2, ',', '.');
				$budget_charge_data[] = $row;
			}
		
			$query_budget_machine = " SELECT m.id AS machine_id,bm.uuid AS budget_machine_uuid, m.name AS machine_name,
																			mc.name AS category_name, m.brand, m.license_plate, m.serial_number, (b.amount/100) AS amount,
																			(b.total_fee/100) AS total_fee, (bm.price_per_hour/100) AS budget_machine_price_per_hour, bm.minimum_rental_period AS budget_machine_minimum_rental_period,
																			(bm.price_per_distance/100) AS budget_machine_price_per_distance, (mf.price_per_hour/100) AS price_per_hour, mf.minimum_rental_period,
																			(mf.price_per_distance/100) AS price_per_distance, mf.distance_amount, (mf.special_hour_fee/100) AS special_hour_fee,
																			DATE_FORMAT(b.created_at, '%d/%m/%Y') AS buget_created_at, DATE_FORMAT(bm.machine_start, '%d/%m/%Y %H:%i') AS machine_start,
																			DATE_FORMAT(bm.machine_end,   '%d/%m/%Y %H:%i') AS machine_end, DATE_FORMAT(bm.machine_break_start, '%d/%m/%Y %H:%i') AS machine_break_start,
																			DATE_FORMAT(bm.machine_break_end,   '%d/%m/%Y %H:%i') AS machine_break_end
														FROM budget_machine bm
														INNER JOIN budget b ON bm.budget_id = b.id
														INNER JOIN machine m ON bm.machine_id = m.id
														INNER JOIN machine_category mc ON m.category_id = mc.id
														LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
														INNER JOIN company c ON m.company_id = c.id
														WHERE b.project_id = (SELECT id FROM project WHERE uuid = ?) AND bm.deleted_at IS NULL";
				$stmt_budget_machine = $this->mysqli->prepare($query_budget_machine);
				$stmt_budget_machine->bind_param('s', $project_uuid);
				$stmt_budget_machine->execute();
				$result_budget_machine = $stmt_budget_machine->get_result();

				// Adicionais de cada machine do budget_machine
				$query_bm_adds = "SELECT ma.uuid AS machine_additional_uuid, at.uuid  AS additional_type_uuid, at.company_id, at.name, at.week_days,
																DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
																DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
																DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
																DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time,   '%H:%i') AS end_time,at.rate
													FROM machine_additional ma
													INNER JOIN additional_type at
															ON at.id = ma.additional_id
														AND at.deleted_at IS NULL
													WHERE ma.machine_id = ?
													AND ma.deleted_at IS NULL
													ORDER BY at.name, ma.id";
				$stmt_bm_adds = $this->mysqli->prepare($query_bm_adds);

				$budget_machine = [];
				while ($row = $result_budget_machine->fetch_assoc()) {

					$row['amount'] = isset($row['amount']) ? number_format($row['amount'], 2, ',', '.') : null;
					$row['total_fee'] = isset($row['total_fee']) ? number_format($row['total_fee'], 2, ',', '.') : null;
					$row['price_per_hour'] = number_format($row['price_per_hour'], 2, ',', '.');
					$row['budget_machine_price_per_hour'] = isset($row['budget_machine_price_per_hour']) ? number_format($row['budget_machine_price_per_hour'], 2, ',', '.') : null;
					$row['budget_machine_price_per_distance'] = isset($row['budget_machine_price_per_distance']) ? number_format($row['budget_machine_price_per_distance'], 2, ',', '.') : null;
					$row['budget_machine_minimum_rental_period'] = isset($row['budget_machine_minimum_rental_period']) ? $row['budget_machine_minimum_rental_period'] : null;
					$row['special_hour_fee'] = number_format($row['special_hour_fee'], 2, ',', '.');
					$row['price_per_distance'] = number_format($row['price_per_distance'], 2, ',', '.');

					// bind_param para executar a query dos adicionais
					$bm_additionals = [];
					$bm_id = (int)$row['machine_id'];
					$stmt_bm_adds->bind_param('i', $bm_id);
					$stmt_bm_adds->execute();
					$res_bm_adds = $stmt_bm_adds->get_result();
					while ($a = $res_bm_adds->fetch_assoc()) {
						$bm_additionals[] = $a;
					}

					// Anexa e remove o id interno se quiser
					$row['additionals'] = $bm_additionals;
					unset($row['machine_id']);

					$budget_machine[] = $row;
				}

			$query_operator = "WITH ci AS (
												SELECT machine_operator_id, MIN(created_at) AS check_in
												FROM operator_check_in
												GROUP BY machine_operator_id
											),
											co AS (
												SELECT machine_operator_id, MIN(created_at) AS check_out
												FROM operator_check_out
												GROUP BY machine_operator_id
											)
											SELECT
												bmo.employee_id AS operator_id, (r.hourly_price / 100) AS hourly_price, r.name AS role_name,
												e.minimum_rental_period, (e.distance_amount / 100) AS distance_amount, op.full_name AS operator_name,
												DATE_FORMAT(bmo.operator_start, '%d/%m/%Y %H:%i') AS operator_start,
												DATE_FORMAT(bmo.operator_end,   '%d/%m/%Y %H:%i') AS operator_end,
												DATE_FORMAT(bmo.operator_break_start, '%d/%m/%Y %H:%i') AS operator_break_start,
												DATE_FORMAT(bmo.operator_break_end,   '%d/%m/%Y %H:%i') AS operator_break_end,bmo.uuid AS budget_machine_operator_uuid,
												DATE_FORMAT(ci.check_in,  '%d/%m/%Y %H:%i') AS check_in, DATE_FORMAT(co.check_out, '%d/%m/%Y %H:%i') AS check_out, FLOOR(TIMESTAMPDIFF(MINUTE, ci.check_in, co.check_out) / 60) AS full_hours
											FROM budget_machine_operator bmo
											LEFT JOIN budget_machine bm ON bmo.budget_machine_id = bm.id
											INNER JOIN budget b ON bm.budget_id = b.id
											LEFT JOIN employee e ON bmo.employee_id = e.id
											LEFT JOIN `role` r ON bmo.role_id = r.id
											LEFT JOIN user op ON e.user_id = op.id
											LEFT JOIN ci ON ci.machine_operator_id = bmo.id
											LEFT JOIN co ON co.machine_operator_id = bmo.id
											WHERE b.project_id = (SELECT id FROM project WHERE uuid = ?) AND bm.deleted_at IS NULL";
			$stmt_operator = $this->mysqli->prepare($query_operator);
			$stmt_operator->bind_param('s', $project_uuid);
			$stmt_operator->execute();
			$result_operator = $stmt_operator->get_result();

			$query_bmo_adds = "SELECT oa.uuid AS operator_additional_uuid, at.uuid   AS additional_type_uuid, at.name, at.week_days,
												DATE_FORMAT(at.holiday_date, '%d/%m/%y') AS holiday_date, DAYNAME(at.holiday_date) AS holiday_weekday_name, WEEKDAY(at.holiday_date) + 1 AS holiday_weekday_num,
												DATE_FORMAT(at.start_date, '%d/%m/%y') AS start_date, DAYNAME(at.start_date) AS start_weekday_name, WEEKDAY(at.start_date) + 1 AS start_weekday_num,
												DATE_FORMAT(at.end_date, '%d/%m/%y') AS end_date, DAYNAME(at.end_date) AS end_weekday_name, WEEKDAY(at.end_date) + 1 AS end_weekday_num,
												DATE_FORMAT(at.start_time, '%H:%i') AS start_time, DATE_FORMAT(at.end_time,   '%H:%i') AS end_time, at.rate
											FROM operator_additional oa
											INNER JOIN additional_type at
													ON at.id = oa.additional_id
												AND at.deleted_at IS NULL
											WHERE oa.operator_id = ?
											AND oa.deleted_at IS NULL
											ORDER BY at.name, oa.id";
			$stmt_bmo_adds = $this->mysqli->prepare($query_bmo_adds);

			$operator = [];
			while ($row = $result_operator->fetch_assoc()) {

				$row['hourly_price'] = number_format($row['hourly_price'], 2, ',', '.');
				$row['distance_amount'] = isset($row['distance_amount']) ? number_format($row['distance_amount'], 2, ',', '.') : null;

				// Busca adicionais desse operador
				$op_additionals = [];
				$bmo_id = (int)$row['operator_id'];
				$stmt_bmo_adds->bind_param('i', $bmo_id);
				$stmt_bmo_adds->execute();
				$res_bmo_adds = $stmt_bmo_adds->get_result();
				while ($a = $res_bmo_adds->fetch_assoc()) {
					$op_additionals[] = $a;
				}

				$row['additionals'] = $op_additionals;
				unset($row['budget_machine_operator_id']);

				$operator[] = $row;
			}

		
			echo json_encode([
				"budget" => $budget_data,
				"budget_machine" => $budget_machine,
				"budget_service_charge" =>$budget_charge_data,
				"operator" => $operator,
			]);
		}

		//  "GET /v1/budget/company/(\w+)
		public function get_budget_by_company_uuid($company_uuid) {

            validate_token();

			$query = " SELECT b.uuid as budget_uuid, b.name as budget_name, p.uuid as project_uuid, p.name as project_name, p.description as project_description, p.identifier, p.max_volume, DATE_FORMAT(b.expected_date, '%d/%m/%Y') AS expected_date, DATE_FORMAT(b.expected_date, '%H:%i') AS expected_hour, DATE_FORMAT(b.expected_date_end, '%d/%m/%Y') AS expected_date_end, p.payer_name, p.cnpj,
												u.full_name as client_name, cli.name AS client_company_name, m.name as machine_name, c.name as company_name, s.name as status_name, DATE_FORMAT(b.created_at, '%d/%m/%Y %H:%i:%s') AS created_at, DATE_FORMAT(b.deleted_at, '%d/%m/%Y %H:%i:%s') AS deleted_at
								FROM budget b
								INNER JOIN project p ON b.project_id = p.id
								LEFT JOIN project_client pc ON pc.project_id = p.id
								LEFT JOIN client cli ON pc.client_id = cli.id
								LEFT JOIN project_user pu ON pu.project_id = p.id
								LEFT JOIN `user` u ON pu.user_id = u.id
								INNER JOIN budget_machine bm ON bm.budget_id = b.id
								INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN company c ON m.company_id = c.id
								INNER JOIN budget_history bh ON bh.id = (
									SELECT bh1.id 
									FROM budget_history bh1 
									WHERE bh1.budget_id = b.id 
									ORDER BY bh1.created_at DESC 
									LIMIT 1
								)
								INNER JOIN status s ON bh.status_id = s.id
								WHERE c.uuid = ? AND p.deleted_at IS NULL
								GROUP BY p.id
								ORDER BY b.created_at DESC;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$company_budget = [];
			while ($row = $result->fetch_assoc()) {
				$company_budget[] = $row;
			}

			echo json_encode(["company_budget" => $company_budget]);
		}

        // "POST /v1/budget/"
		public function create_budget() {
			validate_token();
			$data = validate_payload(["project_uuid", "machine_list"]);
		
			$this->mysqli->begin_transaction();
		
			try {
				$grouped_machines = [];
				foreach ($data->machine_list as $machine) {
					$grouped_machines[$machine->company_id][] = $machine->machine_uuid;
				}
		
				foreach ($grouped_machines as $company_id => $machine_uuids) {
					$query_budget = "INSERT INTO budget (project_id, company_id, name, observation)
									 VALUES ((SELECT id FROM project WHERE uuid = ?), ?, ?, ?)";
		
					$stmt_budget = $this->mysqli->prepare($query_budget);
					$stmt_budget->bind_param('siss', $data->project_uuid, $company_id, $data->name, $data->observation);
		
					if (!$stmt_budget->execute()) {
						throw new Exception("Erro ao cadastrar orçamento para a empresa $company_id.");
					}
		
					$last_budget_id = $this->mysqli->insert_id;
		
					$query_budget_machine = "INSERT INTO budget_machine (machine_id, budget_id)
											 VALUES ((SELECT id FROM machine WHERE uuid = ?), ?)";
					$stmt_budget_machine = $this->mysqli->prepare($query_budget_machine);
		
					foreach ($machine_uuids as $machine_uuid) {
						$stmt_budget_machine->bind_param('si', $machine_uuid, $last_budget_id);
		
						if (!$stmt_budget_machine->execute()) {
							throw new Exception("Erro ao adicionar máquina $machine_uuid ao orçamento.");
						}
					}
		
					// Registrar histórico do orçamento
					$query_history = "INSERT INTO budget_history (budget_id, status_id, details)
									  VALUES (?, 2, ?)";
		
					$stmt_history = $this->mysqli->prepare($query_history);
					$stmt_history->bind_param('is', $last_budget_id, $data->details);
		
					if (!$stmt_history->execute()) {
						throw new Exception("Erro ao registrar histórico do orçamento.");
					}
				}
		
				$this->mysqli->commit();
		
				http_response_code(201);
				echo json_encode(["message" => "Orçamentos criados por empresa e histórico registrado."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}

        // "PUT /v1/budget/"
		public function update_budget() {

			validate_token();
			$data = validate_payload(["budget_uuid", "amount", "total_fee", "condition",]);
		
			$this->mysqli->begin_transaction();
		
			try {
				$query = "UPDATE budget SET observation = ?, amount = ?, total_fee = ?,  `condition` = ?, total_distance = ? WHERE uuid = ?";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('siisis', $data->observation, $data->amount, $data->total_fee, $data->condition, $data->total_distance, $data->budget_uuid);
		
				if (!$stmt->execute()) {
					throw new Exception("Erro ao atualizar orçamento.");
				}

				$query2 = "UPDATE budget_machine SET price_per_hour = ?, price_per_distance = ?, minimum_rental_period = ? WHERE uuid = ?";
				$stmt2 = $this->mysqli->prepare($query2);

				if (!$stmt2) {
					throw new Exception("Erro ao preparar a query: " . $this->mysqli->error);
				}
				
				foreach ($data->budget_machine_uuid_list as $machine) {
					$price_per_hour = intval(floatval(str_replace(',', '.', $machine->budget_machine_price_per_hour)) * 100);
    				$price_per_distance = intval(floatval(str_replace(',', '.', $machine->budget_machine_price_per_distance)) * 100);
					$minimum_rental_period =$machine->budget_machine_minimum_rental_period;

					$stmt2->bind_param('iiis', $price_per_hour, $price_per_distance, $minimum_rental_period, $machine->budget_machine_uuid);
				
					if (!$stmt2->execute()) {
						throw new Exception("Erro ao atualizar dados financeiros da máquina do orçamento: " . $stmt2->error);
					}
				}

				// Insert das taxas
				$query3 = "INSERT INTO budget_service_charge (budget_id, service_charge_id, fee_amount)
								 VALUES ((SELECT id FROM budget WHERE uuid = ?), (SELECT id FROM service_charge WHERE uuid = ?), ?)";
				$stmt3 = $this->mysqli->prepare($query3);

                foreach ($data->service_charge_list as $service_charge) {
					$fee_amount = intval(floatval(str_replace(',', '.', $service_charge->fee_amount)) * 100);

                    $stmt3->bind_param('ssi', $data->budget_uuid, $service_charge->service_charge_uuid, $fee_amount);
                    
                    if (!$stmt3->execute()) {
                        throw new Exception("Erro ao relacionar taxas ao orçamento.");
                    }
                }
		
				$query_history = "INSERT INTO budget_history (budget_id, status_id, details)
								  VALUES ((SELECT id FROM budget WHERE uuid = ?), 11, ?)";
				$stmt_history = $this->mysqli->prepare($query_history);
				$stmt_history->bind_param('ss', $data->budget_uuid, $data->details);
		
				if (!$stmt_history->execute()) {
					throw new Exception("Erro ao registrar histórico do orçamento.");
				}
		
				$this->mysqli->commit();
		
				http_response_code(200);
				echo json_encode(["message" => "Orçamento atualizado."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
		
        // "DELETE /v1/budget/(\w+)"
		public function delete_budget($budget_uuid) {
			$query = "UPDATE budget SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Orçamento desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desativar orçamento."]);
			}
		}

          // "PUT /v1/budget/reactivate/(\w+)"
		public function reactivate_budget($budget_uuid) {
			$query = "UPDATE budget SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Orçamento reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar orçamento."]);
			}
		}
		
		// "POST /v1/budget/available/"
		public function get_available_company() {
			$token = validate_token();
		
			// Recebe: data separada da hora e o fim completo
			$data = validate_payload(["budget_uuid", "expected_date", "hour_date", "expected_date_end"]);
		
			// Monta o datetime de início com a hora recebida
			$hour_date = date('H:i:s', strtotime($data->hour_date));
			$expected_datetime = "{$data->expected_date} {$hour_date}";
		
			// Garante que ambos estão no formato correto
			$expected_date = date('Y-m-d H:i:s', strtotime($expected_datetime));
			$expected_date_end = date('Y-m-d H:i:s', strtotime($data->expected_date_end));
		
			// Verifica se está pelo menos 12 horas à frente
			$expected_dt = new DateTime($expected_date);
			$now_plus_12h = (new DateTime())->add(new DateInterval('PT12H'));
		
			if ($expected_dt < $now_plus_12h) {
				http_response_code(400);
				echo json_encode(["error" => "A data e hora de início devem estar pelo menos 12 horas à frente do momento atual."]);
				exit;
			}
		
			// Consulta máquinas e verifica se estão disponíveis nesse intervalo
			$query = " SELECT b.uuid AS budget_uuid, bm.machine_id, m.company_id, c.name AS company_name, m.name AS machine_name, mc.name AS category_name, mf.minimum_rental_period AS minimum_rental_period,
									CASE 
										WHEN b2.id IS NOT NULL THEN 'Horário ocupado'
										ELSE 'Disponível'
									END AS status
								FROM budget_machine bm
								INNER JOIN budget b ON bm.budget_id = b.id
								INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN machine_franchise mf ON mf.machine_id = m.id
								INNER JOIN machine_category mc ON m.category_id = mc.id
								INNER JOIN company c ON m.company_id = c.id
								LEFT JOIN (
									SELECT DISTINCT bm2.machine_id, b2.id
									FROM budget_machine bm2
									INNER JOIN budget b2 ON bm2.budget_id = b2.id
									WHERE 
										b2.expected_date < ? AND b2.expected_date_end > ?
								) AS b2 ON bm.machine_id = b2.machine_id
								WHERE b.uuid = ?;";
		
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('sss', $expected_date_end, $expected_date, $data->budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();
		
			$available_machines = [];
			while ($row = $result->fetch_assoc()) {
				$available_machines[] = $row;
			}
		
			echo json_encode(["available_machines" => $available_machines]);
		}
		
		// "PUT /v1/budget/accept/([\w-]+)"
		public function accept_budget($budget_uuid) {
			validate_token();

			$data = validate_payload(['expected_date', 'expected_date_end']);

			try {
				$this->mysqli->begin_transaction();
		
				$query = "UPDATE budget SET expected_date = ?, expected_date_end = ? WHERE uuid = ?";
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('sss', $data->expected_date, $data->expected_date_end, $budget_uuid);
				
				if (!$stmt->execute()) {
					throw new Exception("Erro ao aceitar proposta.");
				}
		
				$query2 = "INSERT INTO budget_history (budget_id, status_id) VALUES ((SELECT id FROM budget WHERE uuid = ?), 1)";
				
				$stmt2 = $this->mysqli->prepare($query2);
				$stmt2->bind_param('s', $budget_uuid);
				
				if (!$stmt2->execute()) {
					throw new Exception("Erro ao adicionar status de aceito a um orçamento.");
				}

				$query3 = "UPDATE project SET payer_name = ?, cnpj = ? WHERE id = (SELECT project_id FROM budget WHERE uuid = ?)";
				$stmt3 = $this->mysqli->prepare($query3);
				$stmt3->bind_param('sss', $data->payer_name, $data->cnpj, $budget_uuid);
				
				if (!$stmt3->execute()) {
					throw new Exception("Erro ao adicionar responsável pelo pagamento.");
				}
				
				$this->mysqli->commit();
				http_response_code(200);
				echo json_encode(["message" => "Orçamento aceito e histórico registrado com sucesso."]);

			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}

		// "DELETE /v1/budget/cancel/([\w-]+)"
		public function cancel_budget($budget_uuid) {
			validate_token();

			try {
				$this->mysqli->begin_transaction();
		
				// $query = "UPDATE budget SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
				// $stmt = $this->mysqli->prepare($query);
				// $stmt->bind_param('s', $budget_uuid);	
				
				// if (!$stmt->execute()) {
				// 	throw new Exception("Erro ao cancelar orçamento.");
				// }
		
				$query2 = "INSERT INTO budget_history (budget_id, status_id) VALUES ((SELECT id FROM budget WHERE uuid = ?), 16)";
				
				$stmt2 = $this->mysqli->prepare($query2);
				$stmt2->bind_param('s', $budget_uuid);
				
				if (!$stmt2->execute()) {
					throw new Exception("Erro ao adicionar status de recusado a um orçamento.");
				}
				
				$this->mysqli->commit();
				http_response_code(200);
				echo json_encode(["message" => "Orçamento cancelado e histórico registrado com sucesso."]);

			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
	}