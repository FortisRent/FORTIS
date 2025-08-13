<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class MachineController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		// "GET /v1/machine/"
		public function get_all_machines() {
			$query = " SELECT 	m.uuid as machine_uuid, m.name as name,m.brand as brand, mc.name as category_name, m.description, m.max_volume, m.max_weight, m.max_height, m.max_radius, m.price, m.year_manufacture, m.license_plate, m.serial_number,
												m.chassis_number, m.jib, m.parameters, m.crane_truck_data, m.trailer_data, u.full_name as responsible_name, c.name as company_name, 
												mf.uuid as franchise_uuid, mf.price_per_hour, mf.price_per_hour, mf.minimum_rental_period, mf.price_per_distance, mf.distance_amount, mf.special_hour_fee, mf.observation,
											CASE 
												WHEN m.price IS NULL OR m.price = 0 THEN 'franquia'
												WHEN mf.price_per_hour IS NULL OR mf.price_per_hour = 0 THEN 'fixo'
												ELSE NULL
											END AS price_type
								FROM machine m
								INNER JOIN company c ON m.company_id = c.id
								INNER JOIN user u ON c.responsible_id = u.id
                                INNER JOIN machine_category mc ON m.category_id = mc.id
								LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
								WHERE m.deleted_at IS NULL;";

			$result = $this->mysqli->query($query);

			if ($result) {
				$machines = [];
				while ($row = $result->fetch_assoc()) {
					$row['parameters'] = !empty($row['parameters']) ? json_decode($row['parameters'], true) : null;
					$row['crane_truck_data'] = !empty($row['crane_truck_data']) ? json_decode($row['crane_truck_data'], true) : null;
					$row['trailer_data'] = !empty($row['trailer_data']) ? json_decode($row['trailer_data'], true) : null;
					$machines[] = $row;
					
				}
				echo json_encode(["machines" => $machines]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		// "GET /v1/machine/(\w+)"
		public function get_machine_by_uuid($machine_uuid) {
			$query = "  SELECT 	m.uuid as machine_uuid, m.name as name,m.brand as brand, mc.name as category_name, m.description, m.max_volume, m.max_weight, m.max_height, m.max_radius, m.license_plate, m.serial_number, m.chassis_number, m.jib, m.parameters, m.crane_truck_data, m.trailer_data,
												m.price, m.year_manufacture, u.full_name as responsible_name, c.name as company_name, 
												mf.uuid as franchise_uuid, mf.price_per_hour, mf.price_per_hour, mf.minimum_rental_period, mf.price_per_distance, mf.distance_amount, mf.special_hour_fee, mf.observation,
											CASE 
												WHEN m.price IS NULL OR m.price = 0 THEN 'franquia'
												WHEN mf.price_per_hour IS NULL OR mf.price_per_hour = 0 THEN 'fixo'
												ELSE NULL
											END AS price_type
								FROM machine m
								INNER JOIN company c ON m.company_id = c.id
								INNER JOIN user u ON c.responsible_id = u.id
                                INNER JOIN machine_category mc ON m.category_id = mc.id
								LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
								WHERE m.uuid = ?;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "machine not found"]);
				exit();
			}

			$machine = $result->fetch_assoc();
			$machine['parameters'] = !empty($machine['parameters']) ? json_decode($machine['parameters'], true) : null;
			$machine['crane_truck_data'] = !empty($machine['crane_truck_data']) ? json_decode($machine['crane_truck_data'], true) : null;
			$machine['trailer_data'] = !empty($machine['trailer_data']) ? json_decode($machine['trailer_data'], true) : null;

			echo json_encode(["machine" => $machine]);
		}

		//  "GET /v1/machine/company/(\w+)
		public function get_machine_by_company_uuid($company_uuid) {

			$token = validate_token();

			$query = " SELECT m.uuid, m.name as name, mc.name as category_name,m.brand as brand, m.description, m.max_volume, m.max_weight, m.max_height, m.max_radius, m.license_plate, m.serial_number, m.chassis_number, m.jib, m.parameters, m.crane_truck_data, m.trailer_data, m.price, m.year_manufacture, u.full_name as responsible_name, c.name as company_name, c.id as company_id,
											mf.uuid as franchise_uuid, mf.price_per_hour, mf.price_per_hour, mf.minimum_rental_period, mf.price_per_distance, mf.distance_amount, mf.special_hour_fee, mf.observation,
											CASE 
												WHEN m.price IS NULL OR m.price = 0 THEN 'franquia'
												WHEN mf.price_per_hour IS NULL OR mf.price_per_hour = 0 THEN 'fixo'
												ELSE NULL
											END AS price_type
								FROM machine m
								INNER JOIN company c ON m.company_id = c.id
								INNER JOIN user u ON c.responsible_id = u.id
                                INNER JOIN machine_category mc ON m.category_id = mc.id
								LEFT JOIN machine_franchise mf ON mf.machine_id = m.id
								WHERE m.company_id = (SELECT id FROM company WHERE uuid = ?) AND m.deleted_at IS NULL
								ORDER BY m.created_at DESC;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $company_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$machine = [];
			while ($row = $result->fetch_assoc()) {
				
				$row['parameters'] = cleanJsonData($row['parameters']);
				$row['crane_truck_data'] = cleanJsonData($row['crane_truck_data']);
				$row['trailer_data'] = cleanJsonData($row['trailer_data']);

				$machine[] = $row;
			}

			echo json_encode(["machine" => $machine]);
		}

		// "POST /v1/machine/"
		public function create_machine() {
			$token = validate_token();
		
			$data = validate_payload(["company_uuid", "category_uuid", "name", "description", "year_manufacture", "brand", "license_plate", "serial_number", "parameters", "machine_photo_list"]);
		
			$this->mysqli->begin_transaction();
		
			try {
				$uuid = generate_uuid_v3(64);
		
				$query = "INSERT INTO machine (uuid, company_id, category_id, name, description, max_volume, max_weight, max_height, max_radius, price, year_manufacture, brand, license_plate, serial_number, chassis_number, jib, parameters, crane_truck_data, trailer_data)
						  VALUES (?, (SELECT id FROM company WHERE uuid = ?), (SELECT id FROM machine_category WHERE uuid = ?), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
				$parameters_json = json_encode($data->parameters);
				$crane_truck_data_json = json_encode($data->crane_truck_data);
				$trailer_data_json = json_encode($data->trailer_data);
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('sssssiddisssssissss', $uuid, $data->company_uuid, $data->category_uuid, $data->name, $data->description, $data->max_volume, $data->max_weight, $data->max_height, $data->max_radius, $data->price, $data->year_manufacture,$data->brand, $data->license_plate, $data->serial_number, $data->chassis_number, $data->jib, $parameters_json, $crane_truck_data_json, $trailer_data_json);
				$stmt->execute();
		
				$machine_id = $this->mysqli->insert_id;
		
				if (isset($data->price_per_hour) && $data->price_per_hour) {
		
					$query_franchise = "INSERT INTO machine_franchise (machine_id, price_per_hour, minimum_rental_period, price_per_distance, distance_amount, special_hour_fee, observation) 
										VALUES (?, ?, ?, ?, ?, ?, ?)";
		
					$stmt_franchise = $this->mysqli->prepare($query_franchise);
					$stmt_franchise->bind_param('iddddds', $machine_id, $data->price_per_distance, $data->minimum_rental_period, $data->price_per_distance, $data->distance_amount, $data->special_hour_fee, $data->observation);
					$stmt_franchise->execute();
				}

				$query = "INSERT INTO machine_photo (name, machine_id) VALUES (?, ?)";
				$stmt = $this->mysqli->prepare($query);
				
				foreach ($data->machine_photo_list as $photo) {
					$stmt->bind_param('si', $photo->name, $machine_id);
					$stmt->execute();
				}				
		
				$this->mysqli->commit();
		
				http_response_code(201);
				echo json_encode(["message" => "Máquina e valores cadastrados com sucesso."]);
		
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar máquina: " . $e->getMessage()]);
			}
		}
		

		// "PUT /v1/machine/(\w+)"
		public function update_machine($machine_uuid) {
			validate_token();
		
			$data = validate_payload(["name", "description", "brand", "parameters","year_manufacture"]);
			
			try {
				$this->mysqli->begin_transaction();
		
				$query_machine = "UPDATE machine SET name = ?, description = ?, max_volume = ?, max_height = ?, max_weight = ?, max_radius = ?, price = ?, brand = ?, license_plate = ?, serial_number = ?, chassis_number = ?, jib = ?,  year_manufacture= ?, parameters = ?, crane_truck_data = ?, trailer_data = ?  WHERE uuid = ?";
				$stmt_machine = $this->mysqli->prepare($query_machine);
				$parameters_json = json_encode($data->parameters);
				$crane_truck_data_json = json_encode($data->crane_truck_data);
				$trailer_data_json = json_encode($data->trailer_data);
				$stmt_machine->bind_param('ssddidissssisssss', $data->name, $data->description, $data->max_volume, $data->max_height, $data->max_weight, $data->max_radius, $data->price, $data->brand, $data->license_plate, $data->serial_number, 
				$data->chassis_number, $data->jib, $data->year_manufacture, $parameters_json, $crane_truck_data_json, $trailer_data_json, $machine_uuid);
		
				if (!$stmt_machine->execute()) {
					throw new Exception("Erro ao atualizar máquina.");
				}
		
				if (isset($data->price_per_hour) && $data->price_per_hour) {
					// Verifica se já existe um registro em machine_franchise para a máquina
					$query_check = "SELECT mf.id FROM machine_franchise mf
									JOIN machine m ON mf.machine_id = m.id
									WHERE m.uuid = ?";
					$stmt_check = $this->mysqli->prepare($query_check);
					$stmt_check->bind_param('s', $machine_uuid);
					$stmt_check->execute();
					$stmt_check->store_result();
				
					if ($stmt_check->num_rows > 0) {
						// Já existe, então atualiza
						$query_franchise = "UPDATE machine_franchise SET price_per_hour = ?, minimum_rental_period = ?, price_per_distance = ?, distance_amount = ?, special_hour_fee = ?, observation = ? WHERE machine_id = (SELECT id FROM machine WHERE uuid = ?)";
						$stmt_franchise = $this->mysqli->prepare($query_franchise);
						$stmt_franchise->bind_param('dddddss', $data->price_per_hour, $data->minimum_rental_period, $data->price_per_distance, $data->distance_amount, $data->special_hour_fee, $data->observation, $machine_uuid);
					} else {
						// Não existe, então insere
						$query_franchise = "INSERT INTO machine_franchise (machine_id, price_per_hour, minimum_rental_period, price_per_distance, distance_amount, special_hour_fee, observation) 
											VALUES ((SELECT id FROM machine WHERE uuid = ?), ?, ?, ?, ?, ?, ?)";
						$stmt_franchise = $this->mysqli->prepare($query_franchise);
						$stmt_franchise->bind_param('sddddds', $machine_uuid, $data->price_per_hour, $data->minimum_rental_period, $data->price_per_distance, $data->distance_amount, $data->special_hour_fee, $data->observation);
					}
				
					if (!$stmt_franchise->execute()) {
						throw new Exception("Erro ao salvar valores de franquia da máquina.");
					}
				}
				$this->mysqli->commit();
				http_response_code(200);
				echo json_encode(["message" => "Máquina e valores de franquia atualizados com sucesso."]);
			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
		

		// "DELETE /v1/machine/(\w+)"
		public function delete_machine($machine_uuid) {
			$query = "UPDATE machine SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Máquina desativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desativar máquina."]);
			}
		}

		  // "PUT /v1/machine/reactivate/(\w+)"
		public function reactivate_machine($machine_uuid) {
			$query = "UPDATE machine SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Máquina reativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar máquina."]);
			}
		}

		public function upload_machine_image(){
			$token = validate_token();

			$photo_uuid = generate_uuid_v3(16);

            $query = "	SELECT m.id 
								FROM machine m 
								INNER JOIN company c ON m.company_id = c.id
								INNER JOIN user u ON c.responsible_id = u.id
								WHERE u.uuid = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param('s', $token->uuid);
            $stmt->execute();
            $result = $stmt->get_result();

            $machine_id = $result->fetch_assoc()['id'];

			$target_dir = "uploads/machine_image/";
			$original_file_name = basename($_FILES["machine_image"]["name"]);
			$target_file = $target_dir . $original_file_name;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			if (!$_FILES["machine_image"]["tmp_name"]) {
				http_response_code(409);
				echo json_encode(["error" => "Imagem não encontrada."]);
			}

			// // Check if image file is a valid image
			$check = getimagesize($_FILES["machine_image"]["tmp_name"]);

			if ($check === false) {
				http_response_code(409);
				echo json_encode(["error" => "Imagem inválida."]);
				$uploadOk = 0;
			}

			// Check if file already exists
			if (file_exists($target_file)) {
				http_response_code(409);
				echo json_encode(["error" => "Imagem duplicada."]);
				$uploadOk = 0;
			}

			// Check file size
			if ($_FILES["machine_image"]["size"] > 5242880) {
				http_response_code(409);
				echo json_encode(["error" => "Imagem deve ter menos de 5Mb."]);
				$uploadOk = 0;
			}

			// Allow certain file formats
			$allowed_formats = ["jpg", "jpeg", "png", "gif", "webp"];
			if (!in_array($imageFileType, $allowed_formats)) {
				http_response_code(409);
				echo json_encode(["error" => "Formato não permitido. A imagem deve ser JPG, JPEG, PNG, GIF ou WEBP."]);
				$uploadOk = 0;
			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				http_response_code(409);
				echo json_encode(["error" => "Erro ao enviar imagem."]);
			} else {
				// Generate new file name
				$new_file_name = "machine_img_" . $machine_id . $photo_uuid . "." . $imageFileType;
				$new_target_file = $target_dir . $new_file_name;

				if (move_uploaded_file($_FILES["machine_image"]["tmp_name"], $target_file)) {
					// Rename the file
					if (rename($target_file, $new_target_file)) {

						// // Update user img_url in the database
						// $query = "UPDATE user SET profile_picture_url = ? WHERE uuid = ?";
						// $stmt = $this->mysqli->prepare($query);
						// $stmt->bind_param('ss', $new_target_file, $token->uuid);

						// if ($stmt->execute()) {
							echo json_encode(["machine_photo_url" => "./uploads/machine_image/" . $new_file_name]);
						// } else {
							// echo json_encode(["error" => "Erro ao salvar no banco de dados."]);
						// }

						// $stmt->close();
						// $this->mysqli->close();
					} else {
						echo json_encode(["error" => "Erro ao renomear imagem."]);
					}
				} else {
					echo json_encode(["error" => "Erro no upload."]);
				}
			}
		}

		// public function get_machine_by_params($project_uuid) {
		// 	validate_token();

		// 	$query_select_category = "	SELECT DISTINCT pc.id, pc.name as project_category_name, mc.name as machine_category_name
		// 													FROM project_category pc
		// 													INNER JOIN machine_category_group mcp ON mcp.project_category_id = pc.id
		// 													INNER JOIN machine_category_group_rel mcgr ON mcgr.machine_category_group_id = mcp.id
		// 													INNER JOIN machine_category mc ON mcgr.machine_category_id = mc.id
		// 													INNER JOIN project p ON p.machine_category_id = mc.id
		// 													WHERE p.uuid = ?;";
			
		// 	$stmt = $this->mysqli->prepare($query_select_category);
		// 	$stmt->bind_param('s', $project_uuid);
		// 	$stmt->execute();
		// 	$result = $stmt->get_result();
			
		// 	if ($row = $result->fetch_assoc()) {
		// 		$category_id = $row['id'];
		// 	} else {
		// 		echo json_encode(["error" => "Categoria não encontrada"]);
		// 		return;
		// 	}
			
		// 	// Define qual recomendação utilizar baseado na categoria
		// 	if ($category_id == 2) {
		// 		$query = "	SELECT m.*, c.name as company_name, mc.name as category_name, p.uuid as project_uuid
		// 							FROM machine m
		// 							INNER JOIN company c ON m.company_id = c.id
		// 							INNER JOIN machine_category mc ON m.category_id = mc.id
		// 							INNER JOIN project p ON p.uuid = ?
		// 							INNER JOIN `load` l ON l.project_id = p.id
		// 							WHERE m.category_id = p.machine_category_id
		// 								AND m.max_volume >= p.max_volume
		// 								AND m.max_height >= l.height
		// 								AND m.max_radius >= l.radius
		// 								AND NOT EXISTS(
		// 									SELECT 1
		// 									FROM budget b
		// 									INNER JOIN project p2 ON b.project_id = p2.id
		// 									INNER JOIN budget_history bh ON bh.budget_id = b.id
		// 									WHERE b.machine_id = m.id AND p2.expected_date = p.expected_date AND bh.status_id = 1
		// 								)
		// 							ORDER BY (m.max_volume - p.max_volume), (m.max_height - l.height), (m.max_radius - l.radius) ASC;";
		// 	}
		// 	if ($category_id == 3){
		// 		$query = "	SELECT m.*, c.name as company_name, mc.name as category_name, p.uuid as project_uuid, p.expected_date
		// 							FROM machine m
		// 							INNER JOIN company c ON m.company_id = c.id
		// 							INNER JOIN machine_category mc ON m.category_id = mc.id
		// 							INNER JOIN project p ON p.uuid = ?
		// 							WHERE m.category_id = p.machine_category_id AND m.deleted_at IS NULL
		// 							ORDER BY (m.price) ASC;";
		// 	} else {
		// 		$query = "	SELECT m.*, c.name as company_name, mc.name as category_name, p.uuid as project_uuid, p.expected_date
		// 							FROM machine m
		// 							INNER JOIN company c ON m.company_id = c.id
		// 							INNER JOIN machine_category mc ON m.category_id = mc.id
		// 							INNER JOIN project p ON p.uuid = ?
		// 							WHERE m.category_id = p.machine_category_id AND m.max_volume >= p.max_volume 
		// 								AND NOT EXISTS(
		// 									SELECT 1
		// 									FROM budget b
		// 									INNER JOIN project p2 ON b.project_id = p2.id
		// 									INNER JOIN budget_history bh ON bh.budget_id = b.id
		// 									WHERE b.machine_id = m.id AND p2.expected_date = p.expected_date AND bh.status_id = 1
		// 								)
		// 							ORDER BY (m.max_volume - p.max_volume) ASC;";
		// 	}
			
		// 	$stmt = $this->mysqli->prepare($query);
		// 	$stmt->bind_param('s', $project_uuid);
		// 	$stmt->execute();
		// 	$result = $stmt->get_result();
			
		// 	$machine_params = [];
		// 	while ($row = $result->fetch_assoc()) {
		// 		$machine_params[] = $row;
		// 	}
			
		// 	echo json_encode(["machine_params" => $machine_params]);
		// }

		public function get_machine_by_params($project_uuid) {
			validate_token();
		
			// 🔹 Obtém os IDs dos estágios do projeto
			$query_stages = "SELECT id, JSON_KEYS(parameters) AS param_keys FROM project_stage WHERE project_id = (SELECT id FROM project WHERE uuid = ?)";
			
			$stmt = $this->mysqli->prepare($query_stages);
			$stmt->bind_param('s', $project_uuid);
			$stmt->execute();
			$result = $stmt->get_result();
		
			$stages = [];
			while ($row = $result->fetch_assoc()) {
				$stage_id = $row['id'];
				$param_keys = json_decode($row['param_keys'], true);
				$stages[] = ['id' => $stage_id, 'param_keys' => $param_keys];
			}
		
			if (empty($stages)) {
				echo json_encode(["error" => "Nenhuma etapa encontrada para o projeto"]);
				return;
			}
		
			$machines_by_project = [];
		
			foreach ($stages as $stage) {
				$stage_id = $stage['id'];
				$param_keys = $stage['param_keys'];
		
				// 🔸 Adiciona condição para permitir máquinas sem parâmetros
				$whereClauses = [];
				foreach ($param_keys as $param) {
					$whereClauses[] = "(m.parameters IS NULL OR JSON_EXTRACT(m.parameters, '$.$param') IS NULL OR JSON_EXTRACT(m.parameters, '$.$param') >= IFNULL(JSON_EXTRACT(ps.parameters, '$.$param'), 0))";
				}
		
				$extra_fields = ['max_radius', 'max_weight', 'max_height'];
				foreach ($extra_fields as $field) {
					$whereClauses[] = "(ps.$field IS NULL OR m.$field >= ps.$field)";
				}
		
				$whereSql = implode(" AND ", $whereClauses);
		
				$orderBySql = "";
				if (!empty($param_keys)) {
					$orderClauses = array_map(
						fn($p) => "(JSON_EXTRACT(m.parameters, '$.$p') - JSON_EXTRACT(ps.parameters, '$.$p'))",
						$param_keys
					);
					if (!empty($orderClauses)) {
						$orderBySql = "ORDER BY " . implode(", ", $orderClauses) . " ASC";
					}
				}

		
				$query = "SELECT m.*, m.parameters as parameters, c.name as company_name, mc.name as category_name, p.uuid as project_uuid, p.expected_date
						  FROM machine m
						  INNER JOIN company c ON m.company_id = c.id
						  INNER JOIN machine_category mc ON m.category_id = mc.id
						  INNER JOIN project_stage ps ON ps.id = ?
						  INNER JOIN project p ON p.id = ps.project_id
						  WHERE m.category_id = ps.machine_category_id
						  AND $whereSql
						  $orderBySql;";
		
				$stmt = $this->mysqli->prepare($query);
				$stmt->bind_param('i', $stage_id);
				$stmt->execute();
				$result = $stmt->get_result();
		
				$machines = [];
				while ($row = $result->fetch_assoc()) {
					if (isset($row['parameters'])) {
						// $row['parameters'] = json_decode($row['parameters'], true);
						$row['parameters'] = cleanJsonData($row['parameters']);
						$row['crane_truck_data'] = cleanJsonData($row['crane_truck_data']);
						$row['trailer_data'] = cleanJsonData($row['trailer_data']);
					}
					$machines[] = $row;
				}
		
				$machines_by_project[] = [
					"project_stage_id" => $stage_id,
					"machines" => $machines
				];
			}
		
			echo json_encode(["machines_by_project" => $machines_by_project]);
		}
		
		
		
	}