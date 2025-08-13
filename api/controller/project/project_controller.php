<?php
	require_once('./service/utils.php');

	// {
	// 	"name": "Projeto Construção",
	// 	"description": "Projeto para construção de um edifício comercial.",
	// 	"expected_date": "2024-12-31",
	// 	"machine_list": [
	// 	  {
	// 		"machine_category_uuid": "2mN17XbfbDIcx5iO3nMtzTIsDkUZlxs7yIfU1NXGccbPkRumHKtpYy5SrOfCOt1T",
	// 		"parameters": {
	// 		  "max_weight": 1200,
	// 		  "max_height": "5 tons",
	// 		  "type": "diesel",
	// 		  "height": 100,
	// 		  "width": 50,
	// 		  "length": 150
	// 		},
	// 		"need_operator": 1
	// 	  },
	// 	  {
	// 		"machine_category_uuid": "Ur3uP05EKAZMo9Jsz4eME05wqxsroHUkS7aqXeXPmAmZTd27oWCzSrbAv6C0nlhR",
	// 		"parameters": {
	// 		  "payload_torque": 2500,
	// 		  "max_vertical_reach": 20,
	// 		  "max_horizontal_reach": 30,
	// 		  "body_width": 100,
	// 		  "body_length": 150,
	// 		  "truck_height": 100,
	// 		  "truck_width": 50,
	// 		  "truck_length": 150,
	// 		  "truck_type": "trucado",
	// 		  "max_height": 50
			  
	// 		},
	// 		"need_operator": 0
	// 	  }
	// 	],
	// 	"zip_code": "12345-678",
	// 	"street": "Avenida Central",
	// 	"number_street": "1000",
	// 	"complement": "Próximo ao shopping",
	// 	"neighborhood": "Centro",
	// 	"city_name": "São Paulo",
	// 	"state_name": "SP"
	//   }
	  

	class ProjectController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}
		 # "GET /v1/project/" 
		public function get_all_projects() {
			$query = "  SELECT p.uuid as project_uuid, p.identifier, p.name as project_name, DATE_FORMAT(p.expected_date, '%d/%m/%Y') as expected_date, DATE_FORMAT(p.expected_date, '%W') as day_name, p.start_time, p.end_time, u.full_name as client_name, c.name AS client_company_name, p.payer_name, p.cnpj,
								pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, city.name as city_name, gs.name as state_name, s.name as status_name
								FROM project p
								LEFT JOIN project_client pc ON pc.project_id = p.id
								LEFT JOIN client c ON pc.client_id = c.id
								LEFT JOIN project_user pu ON pu.project_id = p.id
								LEFT JOIN `user` u ON pu.user_id = u.id
								INNER JOIN project_address pa ON pa.project_id = p.id
								LEFT JOIN budget b ON b.project_id = p.id
								LEFT JOIN budget_history bh ON bh.budget_id = b.id
								LEFT JOIN status s ON bh.status_id = s.id
								INNER JOIN city city ON pa.city_id = city.id
								INNER JOIN geo_state gs ON city.state_id = gs.id
								WHERE p.deleted_at IS NULL
								ORDER BY p.created_at DESC;";

			$result = $this->mysqli->query($query);

			if ($result) {
				$project = [];
				while ($row = $result->fetch_assoc()) {
					$project[] = $row;
				}
				echo json_encode(["project" => $project]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		# "GET /v1/project/(\w+)"       
		public function get_project_by_uuid($project_uuid) {
			validate_token();
		
			$query_project = "SELECT p.uuid AS project_uuid, p.name as project_name, p.description as project_description, DATE_FORMAT(p.expected_date, '%d/%m/%Y') as expected_date, DATE_FORMAT(p.created_at, '%d/%m/%Y') as created_at,
														u.full_name as user_name, u.email, u.phone, u.cpf AS user_cpf, c.name AS client_name, c.phone AS client_phone, c.cpf AS client_cpf, c.email AS client_email, p.payer_name, p.cnpj, pa.uuid AS project_address_uuid, pa.zip_code, pa.street, pa.number_street, pa.complement, pa.neighborhood, city.name as city_name, gs.name as state_name, s.name as status_name
											FROM project p
											LEFT JOIN project_client pc ON pc.project_id = p.id
											LEFT JOIN client c ON pc.client_id = c.id
											LEFT JOIN project_user pu ON pu.project_id = p.id
											LEFT JOIN `user` u ON pu.user_id = u.id
											INNER JOIN project_address pa ON pa.project_id = p.id
											INNER JOIN city city ON pa.city_id = city.id
											INNER JOIN geo_state gs ON city.state_id = gs.id
											LEFT JOIN budget b ON b.project_id = p.id
											LEFT JOIN budget_history bh ON bh.id = (
												SELECT bh1.id
												FROM budget_history bh1
												WHERE bh1.budget_id = b.id
												ORDER BY bh1.created_at DESC
												LIMIT 1
											)
											LEFT JOIN status s ON s.id = bh.status_id
											WHERE p.uuid = ? AND p.deleted_at IS NULL";
		
			$stmt_project = $this->mysqli->prepare($query_project);
			$stmt_project->bind_param('s', $project_uuid);
			$stmt_project->execute();
			$result_project = $stmt_project->get_result();
			$project_data = $result_project->fetch_assoc();
		
			$query_project_stage = "SELECT mc.name as name_machine_category, ps.parameters, ps.need_operator
														FROM project_stage ps
														INNER JOIN machine_category mc ON ps.machine_category_id = mc.id
														WHERE ps.project_id = (SELECT id FROM project WHERE uuid = ?)";
		
			$stmt_project_stage = $this->mysqli->prepare($query_project_stage);
			$stmt_project_stage->bind_param('s', $project_uuid);
			$stmt_project_stage->execute();
			$result_project_stage = $stmt_project_stage->get_result();
		
			$project_stages = [];
			while ($row = $result_project_stage->fetch_assoc()) {
				$row['parameters'] = json_decode($row['parameters'], true);
				$project_stages[] = $row;
			}
		
			echo json_encode([
				"project" => $project_data,
				"project_stages" => $project_stages
			]);
		}

		// "GET /v1/project/logged/" 
		public function get_project_by_logged() {
			$token = validate_token();

			$query = " SELECT p.uuid as project_uuid, p.identifier, p.name as project_name, DATE_FORMAT(p.expected_date, '%d/%m/%Y') as expected_date, DATE_FORMAT(p.expected_date, '%W') as day_name, p.start_time, p.end_time, u.full_name as client_name, pa.zip_code, pa.street, 
											pa.number_street, pa.complement, pa.neighborhood, c.name as city_name, gs.name as state_name, s.name as status_name
								FROM project p
								INNER JOIN user u ON p.user_id = u.id
								INNER JOIN project_address pa ON pa.project_id = p.id
								LEFT JOIN budget b ON b.project_id = p.id
								LEFT JOIN budget_history bh ON bh.id = (
									SELECT bh1.id
									FROM budget_history bh1
									WHERE bh1.budget_id = b.id
									ORDER BY bh1.created_at DESC
									LIMIT 1
								)
								LEFT JOIN status s ON bh.status_id = s.id
								INNER JOIN city c ON pa.city_id = c.id
								INNER JOIN geo_state gs ON c.state_id = gs.id
								WHERE p.user_id = (SELECT id FROM user WHERE uuid = ?) AND p.deleted_at IS NULL
								ORDER BY p.created_at DESC;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$project = [];
			while ($row = $result->fetch_assoc()) {
				$project[] = $row;
			}

			echo json_encode(["project" => $project]);
		}

		// "GET /v1/project/company/([\w-]+)"
		public function get_project_by_company_uuid($company_uuid) {
			validate_token();

			$query = "  SELECT p.uuid as project_uuid, b.uuid as budget_uuid, p.identifier, p.name as project_name, DATE_FORMAT(p.expected_date, '%d/%m/%Y') as expected_date, DATE_FORMAT(p.expected_date, '%W') as day_name, p.start_time, p.end_time, 
								u.full_name as client_name, pa.zip_code, pa.street, cli.name AS client_company_name, p.payer_name, p.cnpj,
											pa.number_street, pa.complement, pa.neighborhood, city.name as city_name, gs.name as state_name, s.name as status_name
								FROM project p
								LEFT JOIN project_client pc ON pc.project_id = p.id
								LEFT JOIN client cli ON pc.client_id = cli.id
								LEFT JOIN project_user pu ON pu.project_id = p.id
								LEFT JOIN `user` u ON pu.user_id = u.id
								INNER JOIN project_address pa ON pa.project_id = p.id
								INNER JOIN budget b ON b.project_id = p.id
								INNER JOIN budget_machine bm ON bm.budget_id = b.id
								INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN company c ON m.company_id = c.id
								LEFT JOIN budget_history bh ON bh.id = (
									SELECT bh1.id 
									FROM budget_history bh1 
									WHERE bh1.budget_id = b.id 
									ORDER BY bh1.created_at DESC 
									LIMIT 1
								)
								LEFT JOIN status s ON bh.status_id = s.id
								INNER JOIN city city ON pa.city_id = city.id
								INNER JOIN geo_state gs ON city.state_id = gs.id
								WHERE c.uuid = ?
								GROUP BY p.id
								ORDER BY p.created_at DESC;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$project = [];
			while ($row = $result->fetch_assoc()) {
				$project[] = $row;
			}

			echo json_encode(["project" => $project]);
		}

		// "GET /v1/project/budget/([\w-]+)"
		public function get_project_by_budget_uuid($budget_uuid) {
			validate_token();

			$query_project = "SELECT	p.uuid AS project_uuid, p.name AS project_name, p.description project_description, DATE_FORMAT(p.expected_date, '%d/%m/%Y %H:%i') AS expected_date, DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') AS created_at,
															mc.name AS name_machine_category, p.payer_name, p.cnpj,  u.full_name AS user_name, u.email, u.phone, c.name AS client_name, c.phone AS client_phone, c.email AS client_email, pa.zip_code, pa.street, pa.number_street,
															pa.complement, pa.neighborhood, pa.city_id, pa.state_id, b.uuid AS budget_uuid, s.name AS status_name, ps.need_operator, ps.parameters
											FROM project p
											LEFT JOIN project_client pc ON pc.project_id = p.id
											LEFT JOIN client c ON pc.client_id = c.id
											LEFT JOIN project_user pu ON pu.project_id = p.id
											LEFT JOIN `user` u ON pu.user_id = u.id
											LEFT JOIN project_stage ps ON ps.project_id = p.id
											INNER JOIN machine_category mc ON ps.machine_category_id = mc.id
											LEFT JOIN `load` l ON l.project_id = p.id
											INNER JOIN project_address pa ON pa.project_id = p.id
											INNER JOIN budget b ON b.project_id = p.id
											INNER JOIN budget_history bh ON bh.id = (
												SELECT bh1.id 
												FROM budget_history bh1 
												WHERE bh1.budget_id = b.id 
												ORDER BY bh1.created_at DESC 
												LIMIT 1
											)
											INNER JOIN status s ON bh.status_id = s.id
											WHERE b.uuid = ? AND p.deleted_at IS NULL;";
		
			$stmt_project = $this->mysqli->prepare($query_project);
			$stmt_project->bind_param('s', $budget_uuid);
			$stmt_project->execute();
			$result_project = $stmt_project->get_result();
			$project_data = $result_project->fetch_assoc();
		
			$query_project_stage = "SELECT mc.name as name_machine_category, ps.parameters, ps.need_operator,m.name as machine_name, m.price / 100 as fixed_price, mf.price_per_hour / 100 as price_per_hour, 
																	mf.minimum_rental_period / 100 as minimum_rental_period, mf.price_per_distance / 100 as price_per_distance, mf.distance_amount / 100 as distance_amount, mf.special_hour_fee / 100 as special_hour_fee
														FROM project_stage ps
														INNER JOIN machine_category mc ON ps.machine_category_id = mc.id
														LEFT JOIN budget b ON b.project_id = ps.project_id
														LEFT JOIN budget_machine bm ON bm.budget_id = b.id
														LEFT JOIN machine m ON bm.machine_id = m.id
														LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
														WHERE ps.project_id = (SELECT id FROM project WHERE uuid = ?)";
		
			$stmt_project_stage = $this->mysqli->prepare($query_project_stage);
			$stmt_project_stage->bind_param('s', $budget_uuid);
			$stmt_project_stage->execute();
			$result_project_stage = $stmt_project_stage->get_result();
		
			$project_stages = [];
			while ($row = $result_project_stage->fetch_assoc()) {
				$row['parameters'] = json_decode($row['parameters'], true);
				$project_stages[] = $row;
			}
		
			echo json_encode([
				"project" => $project_data,
				"project_stages" => $project_stages
			]);
		}

		// "GET /v1/project/list/company/([\w-]+)"
		public function get_project_list_financial_by_company_uuid($company_uuid) {
			validate_token();

			$query = "  SELECT p.uuid as project_uuid, b.uuid as budget_uuid, p.identifier, p.name as project_name, u.full_name AS user_name
								FROM project p
								INNER JOIN user u ON p.user_id = u.id
								INNER JOIN budget b ON b.project_id = p.id
								INNER JOIN machine m ON bm.machine_id = m.id
								INNER JOIN company c ON m.company_id = c.id
								WHERE c.uuid = ?
								GROUP BY p.id
								ORDER BY p.created_at DESC;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$project = [];
			while ($row = $result->fetch_assoc()) {
				$project[] = $row;
			}

			echo json_encode(["project" => $project]);
		}
    
		public function get_project_checks($project_uuid) {
			validate_token();
		
			// 1. Buscar dados do projeto
			$query_project = "SELECT uuid AS project_uuid, name AS project_name, identifier FROM project WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query_project);
			$stmt->bind_param("s", $project_uuid);
			$stmt->execute();
			$result = $stmt->get_result();
			$project_data = $result->fetch_assoc();
		
			if (!$project_data) {
				echo json_encode(["error" => "Projeto não encontrado"]);
				return;
			}
		
			// 2. Buscar funcionários do projeto
			$query_employees = "SELECT DISTINCT e.uuid AS employee_uuid, u.full_name AS employee_name, u.phone, u.email, r.name AS role_name
													FROM project p
													INNER JOIN budget b ON b.project_id = p.id
													INNER JOIN budget_machine bm ON bm.budget_id = b.id
													INNER JOIN budget_machine_operator bmo ON bmo.budget_machine_id = bm.id
													INNER JOIN employee e ON bmo.employee_id = e.id
													INNER JOIN user u ON e.user_id = u.id
													INNER JOIN role r ON e.role_id = r.id
													WHERE p.uuid = ? AND e.deleted_at IS NULL";
			$stmt = $this->mysqli->prepare($query_employees);
			$stmt->bind_param("s", $project_uuid);
			$stmt->execute();
			$result = $stmt->get_result();
		
			$employees = [];
		
			while ($row = $result->fetch_assoc()) {
				$employee_uuid = $row['employee_uuid'];
		
				// 3. Buscar check-ins para o funcionário
				$checkin_query = " SELECT oci.description, DATE_FORMAT(oci.created_at, '%H:%i') AS hour, DATE_FORMAT(oci.created_at, '%d/%m/%Y') AS date
													FROM operator_check_in oci
													INNER JOIN budget_machine_operator bmo ON oci.machine_operator_id = bmo.id
													INNER JOIN employee e ON bmo.employee_id = e.id
													WHERE e.uuid = ?";
				$stmt_checkin = $this->mysqli->prepare($checkin_query);
				$stmt_checkin->bind_param("s", $employee_uuid);
				$stmt_checkin->execute();
				$checkins_result = $stmt_checkin->get_result();
				$checkins = $checkins_result->fetch_all(MYSQLI_ASSOC);
		
				// 4. Buscar check-outs para o funcionário
				$checkout_query = "SELECT oco.description, DATE_FORMAT(oco.created_at, '%H:%i') AS hour, DATE_FORMAT(oco.created_at, '%d/%m/%Y') AS date
													FROM operator_check_out oco
													INNER JOIN budget_machine_operator bmo ON oco.machine_operator_id = bmo.id
													INNER JOIN employee e ON bmo.employee_id = e.id
													WHERE e.uuid = ?";
				$stmt_checkout = $this->mysqli->prepare($checkout_query);
				$stmt_checkout->bind_param("s", $employee_uuid);
				$stmt_checkout->execute();
				$checkouts_result = $stmt_checkout->get_result();
				$checkouts = $checkouts_result->fetch_all(MYSQLI_ASSOC);
		
				// Adiciona o funcionário ao array final
				$employees[] = [
					"employee_uuid" => $row['employee_uuid'],
					"employee_name" => $row["employee_name"],
					"phone" => $row["phone"],
					"email" => $row["email"],
					"role_name" => $row["role_name"],
					"checkins" => $checkins,
					"checkouts" => $checkouts
				];
			}
		
			// Resposta final
			echo json_encode([
				"project_data" => $project_data,
				"employees" => $employees
			]);
		}
		
		# "POST /v1/project/" 
		public function create_project_user() {

			$token = validate_token();
			$data = validate_payload(["machine_list", "neighborhood", "city_name","state_name"]);

			$this->mysqli->begin_transaction();

			try {
			$identifier = generate_identifier(16);
			$project_uuid = generate_uuid_v3(64);

			$query_project = "INSERT INTO project (uuid, name, description, identifier, user_id, expected_date, payer_name, cnpj) VALUES (?, ?, ?, ?, (SELECT id FROM user WHERE uuid = ?), ?, ?, ?)";
			$stmt_project = $this->mysqli->prepare($query_project);
			$stmt_project->bind_param('ssssssss', $project_uuid, $data->name, $data->description, $identifier, $token->uuid, $data->expected_date, $data->payer_name, $data->cnpj);
			$stmt_project->execute();

			$project_id = $this->mysqli->insert_id;

			$query_project_user = "INSERT INTO project_user (project_id, user_id) VALUES (?, (SELECT id FROM user WHERE uuid = ?))";
			$stmt_project_user = $this->mysqli->prepare($query_project_user);
			$stmt_project_user->bind_param('ss', $project_id, $token->uuid);
			$stmt_project_user->execute();

			$query_project_stage = "INSERT INTO project_stage (project_id, machine_category_id, parameters, max_height, max_weight, max_radius, need_operator) VALUES (?, (SELECT id FROM machine_category WHERE uuid = ?), ?, ?, ?, ?, ?)";
			$stmt_project_stage = $this->mysqli->prepare($query_project_stage);

			foreach ($data->machine_list as $machine) {

				$parameters_json = json_encode($machine->parameters);

				$stmt_project_stage->bind_param('issiiii', $project_id, $machine->machine_category_uuid, $parameters_json, $machine->max_height, $machine->max_weight, $machine->max_radius, $machine->need_operator);
				$stmt_project_stage->execute();
			}

			$query_project_address = "INSERT INTO project_address (uuid, project_id, zip_code, street, number_street, complement, neighborhood, city_id) VALUES (?, ?, ?, ?, ?, ?, ?, (SELECT c.id FROM city c INNER JOIN geo_state gs ON c.state_id = gs.id WHERE c.name = ? AND gs.abbreviation = ?))";
			$stmt_project_address = $this->mysqli->prepare($query_project_address);
			$project_address_uuid = generate_uuid_v3(64);

			$stmt_project_address->bind_param('sisssssss', $project_address_uuid, $project_id, $data->zip_code, $data->street, $data->number_street, $data->complement, $data->neighborhood, $data->city_name,$data->state_name);
			$stmt_project_address->execute();

			$this->mysqli->commit();

			http_response_code(201);
			echo json_encode([
				"message" => "Projeto, etapas do projeto e endereço cadastrados com sucesso.",
				"project_uuid" => $project_uuid
			]);
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar o projeto: " . $e->getMessage()]);
			}
		}

		# "POST /v1/project/company/" 
		public function create_project_client() {

			$token = validate_token();
			$data = validate_payload(["machine_list", "neighborhood", "city_name","state_name"]);

			$this->mysqli->begin_transaction();

			try {
			$identifier = generate_identifier(16);
			$project_uuid = generate_uuid_v3(64);

			$query_project = "INSERT INTO project (uuid, name, description, identifier, expected_date, payer_name, cnpj) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$stmt_project = $this->mysqli->prepare($query_project);
			$stmt_project->bind_param('sssssss', $project_uuid, $data->name, $data->description, $identifier, $data->expected_date, $data->payer_name, $data->cnpj);
			$stmt_project->execute();

			$project_id = $this->mysqli->insert_id;

			$query_project_client = "INSERT INTO project_client (project_id, client_id) VALUES (?, (SELECT id FROM client WHERE uuid = ?))";
			$stmt_project_client = $this->mysqli->prepare($query_project_client);
			$stmt_project_client->bind_param('ss', $project_id, $data->client_uuid);
			$stmt_project_client->execute();

			$query_project_stage = "INSERT INTO project_stage (project_id, machine_category_id, parameters, max_height, max_weight, max_radius, need_operator) VALUES (?, (SELECT id FROM machine_category WHERE uuid = ?), ?, ?, ?, ?, ?)";
			$stmt_project_stage = $this->mysqli->prepare($query_project_stage);

			foreach ($data->machine_list as $machine) {

				$parameters_json = json_encode($machine->parameters);

				$stmt_project_stage->bind_param('issiiii', $project_id, $machine->machine_category_uuid, $parameters_json, $machine->max_height, $machine->max_weight, $machine->max_radius, $machine->need_operator);
				$stmt_project_stage->execute();
			}

			$query_project_address = "INSERT INTO project_address (uuid, project_id, zip_code, street, number_street, complement, neighborhood, city_id) VALUES (?, ?, ?, ?, ?, ?, ?, (SELECT c.id FROM city c INNER JOIN geo_state gs ON c.state_id = gs.id WHERE c.name = ? AND gs.abbreviation = ?))";
			$stmt_project_address = $this->mysqli->prepare($query_project_address);
			$project_address_uuid = generate_uuid_v3(64);

			$stmt_project_address->bind_param('sisssssss', $project_address_uuid, $project_id, $data->zip_code, $data->street, $data->number_street, $data->complement, $data->neighborhood, $data->city_name,$data->state_name);
			$stmt_project_address->execute();

			$this->mysqli->commit();

			http_response_code(201);
			echo json_encode([
				"message" => "Projeto, etapas do projeto e endereço cadastrados com sucesso.",
				"project_uuid" => $project_uuid
			]);
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar o projeto: " . $e->getMessage()]);
			}
		}
			
		// POST /v1/project/lifting/
		// public function create_project_lifting() {
		// 	$token = validate_token();

		// 	$data = validate_payload(["machine_category_uuid", "city_name", "count", "weight", "length", "width", "height", "lifting_height", "radius","state_name",]);


		// 	$this->mysqli->begin_transaction();

		// 	try {
		// 		$identifier = generate_identifier(16);
		// 		$project_uuid = generate_uuid_v3(64);
		// 		$query_project = "INSERT INTO project (uuid, name, description, identifier, user_id, machine_category_id, max_volume, expected_date) VALUES (?, ?, ?, ?, (SELECT id FROM user WHERE uuid = ?), (SELECT id FROM machine_category WHERE uuid = ?), ?, ?)";
		// 		$stmt_project = $this->mysqli->prepare($query_project);
		// 		$stmt_project->bind_param('ssssssds', $project_uuid, $data->name, $data->description, $identifier, $token->uuid, $data->machine_category_uuid, $data->max_volume, $data->expected_date);
		// 		$stmt_project->execute();

		// 		$project_id = $this->mysqli->insert_id;

		// 		$project_address_uuid = generate_uuid_v3(64);
		// 		$query_project_address = "INSERT INTO project_address (uuid, project_id, zip_code, street, number_street, complement, neighborhood, city_id) VALUES (?, ?, ?, ?, ?, ?, ?, (SELECT c.id FROM city c INNER JOIN geo_state gs ON c.state_id = gs.id WHERE c.name = ? AND gs.abbreviation = ?))";
		// 		$stmt_project_address = $this->mysqli->prepare($query_project_address);
		// 		$stmt_project_address->bind_param('sisssssss', $project_address_uuid, $project_id, $data->zip_code, $data->street, $data->number_street, $data->complement, $data->neighborhood, $data->city_name,$data->state_name);
		// 		$stmt_project_address->execute();

		// 		$query_project_stage = "INSERT INTO project_stage (project_id, machine_category_id, parameters, need_operator) VALUES (?, (SELECT id FROM machine_category WHERE uuid = ?), ?, ?)";
		// 		$stmt_project_stage = $this->mysqli->prepare($query_project_stage);

		// 		foreach ($data->machine_list as $machine) {
		// 			$parameters_json = json_encode($machine->parameters);

		// 			$stmt_project_stage->bind_param('issi', $project_id, $machine->machine_category_uuid, $parameters_json, $machine->need_operator);
		// 			$stmt_project_stage->execute();
		// 		}

		// 		// $load_uuid = generate_uuid_v3(64);
		// 		// $query_load = "INSERT INTO `load` (uuid, name, project_id, count, weight, length, width, height, lifting_height, radius, total_value) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		// 		// $stmt_load = $this->mysqli->prepare($query_load);
		// 		// $stmt_load->bind_param('ssiiddddddd', $load_uuid, $data->load_name, $project_id, $data->count, $data->weight, $data->length, $data->width, $data->height, $data->lifting_height, $data->radius, $data->total_value);
		// 		// $stmt_load->execute();

		// 		$this->mysqli->commit();

		// 		http_response_code(201);
		// 		echo json_encode([
		// 			"message" => "Projeto e endereço cadastrados com sucesso.",
		// 			"project_uuid" => $project_uuid
		// 		]);
		// 	} catch (Exception $e) {
		// 		$this->mysqli->rollback();
		// 		http_response_code(500);
		// 		echo json_encode(["message" => "Erro ao cadastrar o projeto: " . $e->getMessage()]);
		// 	}
		// }
			
		public function update_project(){
			$token = validate_token();

			// $data = validate_payload(["full_name", "phone", "email", "cpf", "birthdate"]);

			// $stmt = $this->mysqli->prepare("UPDATE user SET full_name = ?, cpf = ?, phone = ?, email = ?, birthdate = ?
			//                                 							WHERE uuid = ?");

			// $stmt->bind_param('ssssss', $data->full_name, $data->cpf, $data->phone, $data->email, $data->birthdate, $user_uuid);

			// if ($stmt->execute()) {
			// 	http_response_code(200);
			// 	echo json_encode(["message" => "Usuário atualizado."]);
			// 	exit();
			// } else {
			// 	http_response_code(500);
			// 	echo json_encode(["message" => "Erro ao atualizar usuário."]);
			// 	exit();
			// }
		}
			
		// DELETE /v1/project/([\w-]+)
		public function delete_project($project_uuid) {
			$query = "UPDATE project SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);

			if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Projeto desativado."]);
			} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao deletar projeto."]);
			}
		}

		  // "PUT /v1/project/reactivate/(\w+)"
		public function reactivate_project($project_uuid) {
			$query = "UPDATE project SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Projeto reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar projeto."]);
			}
		}

	}