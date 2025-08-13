<?php
	require_once('./service/utils.php');

	class UserController {

		private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

		// "GET /v1/user/"
		public function get_users() {
            $token = validate_token();

            try {
                $query = "	SELECT u.id, u.uuid, u.full_name, u.email, u.phone, u.cpf, DATE_FORMAT(u.birthdate, '%d/%m/%Y') as birthdate, u.profile_picture_url, DATE_FORMAT(u.created_at, '%d/%m/%Y') as created_at,
									ua.uuid as address_uuid, ua.zip_code, ua.street, ua.number_street, ua.complement, ua.neighborhood, c.name as city_name, gs.name as state_name
									FROM user u
									LEFT JOIN user_address ua ON ua.user_id = u.id
									LEFT JOIN city c ON ua.city_id = c.id
									LEFT JOIN geo_state gs ON c.state_id = gs.id
									WHERE deleted_at IS NULL";
                $result = $this->mysqli->query($query);

                $users = [];
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row;
                }

                echo json_encode(["users" => $users]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		//"GET /v1/user/logged/"
        public function get_user_by_uuid_logged() {
			$token = validate_token();
            try {
                $query = "	SELECT u.id, u.uuid, u.full_name, u.email, u.phone, u.cpf, DATE_FORMAT(u.birthdate, '%d/%m/%Y') as birthdate, u.profile_picture_url, DATE_FORMAT(u.created_at, '%d/%m/%Y') as created_at,
									ua.uuid as address_uuid, ua.zip_code, ua.street, ua.number_street, ua.complement, ua.neighborhood, c.name as city_name, gs.name as state_name
									FROM user u
									LEFT JOIN user_address ua ON ua.user_id = u.id
									LEFT JOIN city c ON ua.city_id = c.id
									LEFT JOIN geo_state gs ON c.state_id = gs.id
									WHERE u.uuid = ?";
                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $token->uuid);
                $stmt->execute();
                $result = $stmt->get_result();

                $user = $result->fetch_assoc();

                if (!$user) {
                    http_response_code(404);
                    echo json_encode(["error" => "User not found"]);
                    exit();
                }

                echo json_encode(["user" => $user]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		// "GET /v1/user/([\w-]+)"
		public function get_user_by_uuid($user_uuid) {
			$token = validate_token();
            try {
                $query = "	SELECT u.id, u.uuid, u.full_name, u.email, u.phone, u.cpf, DATE_FORMAT(u.birthdate, '%d/%m/%Y') as birthdate, u.profile_picture_url, DATE_FORMAT(u.created_at, '%d/%m/%Y') as created_at,
									ua.uuid as address_uuid, ua.zip_code, ua.street, ua.number_street, ua.complement, ua.neighborhood, c.name as city_name, gs.name as state_name
									FROM user u
									LEFT JOIN user_address ua ON ua.user_id = u.id
									LEFT JOIN city c ON ua.city_id = c.id
									LEFT JOIN geo_state gs ON c.state_id = gs.id
									WHERE uuid = ? AND deleted_at IS NULL";
                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $user_uuid);
                $stmt->execute();
                $result = $stmt->get_result();

                $user = $result->fetch_assoc();

                if (!$user) {
                    http_response_code(404);
                    echo json_encode(["error" => "User not found"]);
                    exit();
                }

                echo json_encode(["user" => $user]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		// "GET /v1/user/logged/profile/" 
		public function get_profiles() {

			$token = validate_token();
		
			$query2 = "SELECT COUNT(*) AS total
								FROM company c
								WHERE c.responsible_id = (SELECT id FROM user WHERE uuid = ?)";

			$stmt2 = $this->mysqli->prepare($query2);
			$stmt2->bind_param('s', $token->uuid);
			$stmt2->execute();
			$result2 = $stmt2->get_result();
			$row = $result2->fetch_assoc();

			$responsible_company = $row['total'] > 0;

			$query3 = "SELECT c.id, c.uuid, c.name AS company_name, c.cnpj, DATE_FORMAT(c.created_at, '%d/%m/%Y') AS created_at, u.full_name AS user_name
								FROM company c
								INNER JOIN user u ON c.responsible_id = u.id 
								WHERE c.responsible_id = (SELECT id FROM user WHERE uuid = ?);";
		
			$stmt3 = $this->mysqli->prepare($query3);
			$stmt3->bind_param('s', $token->uuid);
			$stmt3->execute();
			$result3 = $stmt3->get_result();

			$company = [];
			while ($row = $result3->fetch_assoc()) {
				$company[] = $row;
			}

			$query4 = "SELECT COUNT(*) AS total 
								FROM employee e
								WHERE e.is_invite_accepted = 1 
									AND e.user_id = (SELECT id FROM user WHERE uuid = ?)";

			$stmt4 = $this->mysqli->prepare($query4);
			$stmt4->bind_param('s', $token->uuid);
			$stmt4->execute();
			$result4 = $stmt4->get_result();
			$row = $result4->fetch_assoc();

			$employee = $row['total'] > 0;

			$query5 = "SELECT c.uuid AS company_uuid, c.name AS company_name, c.cnpj, u.full_name AS user_name, r.name AS role_name, e.uuid AS employee_uuid
								FROM employee e
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
                                INNER JOIN user u ON e.user_id = u.id
								WHERE e.user_id = (SELECT id FROM user WHERE uuid = ?) AND e.is_invite_accepted = 1 AND e.deleted_at IS NULL;";
		
			$stmt5 = $this->mysqli->prepare($query5);
			$stmt5->bind_param('s', $token->uuid);
			$stmt5->execute();
			$result5 = $stmt5->get_result();

			$employee_company = [];
			while ($row = $result5->fetch_assoc()) {
				$employee_company[] = $row;
			}
		

			$response = [
				'company_responsible' => $responsible_company,
				'company' => $company,
				'employee' => $employee,
				'employee_company' => $employee_company
			];
		
			echo json_encode($response);
		}
		// "POST /v1/user/"
        public function create_user()
        {
            $data = validate_payload(["full_name", "password", "phone"]);

            try {
                // Check if phone already exists
                $query3 = "SELECT phone FROM user WHERE phone = ?";
                $stmt3 = $this->mysqli->prepare($query3);
                $stmt3->bind_param('s', $data->phone);
                $stmt3->execute();
                $stmt3->store_result();

                if ($stmt3->num_rows > 0) {
                    http_response_code(409);
                    echo json_encode(["message" => "Telefone já está cadastrado."]);
                    exit();
                }

                // Hash password
                $hashed_password = sha1($data->password);

                $stmt = $this->mysqli->prepare("INSERT INTO user(uuid, full_name, password, cpf, phone, email, birthdate, profile_picture_url) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?);");

                $uuid = random_str(32);

                $stmt->bind_param('ssssssss', $uuid, $data->full_name, $hashed_password, $data->cpf, $data->phone, $data->email, $data->birthdate, $data->profile_picture_url);

                if ($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode(["message" => "Usuário cadastrado."]);
                    exit();
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erro ao cadastrar usuário."]);
                    exit();
                }
            } catch (Exception $e) {
                http_response_code(500);
                return json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		// "PUT /v1/user/([\w-]+)"
		public function update_user($user_uuid){
			$token = validate_token();

			$data = validate_payload(["full_name", "phone", "email", "cpf", "birthdate"]);

            $stmt = $this->mysqli->prepare("UPDATE user SET full_name = ?, cpf = ?, phone = ?, email = ?, birthdate = ?
                                            							WHERE uuid = ?");

			$stmt->bind_param('ssssss', $data->full_name, $data->cpf, $data->phone, $data->email, $data->birthdate, $user_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Usuário atualizado."]);
				exit();
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar usuário."]);
				exit();
			}
		}

		// "PUT /v1/user/logged/"
		public function update_user_logged(){
			$token = validate_token();

			$data = validate_payload(["full_name", "phone", "cpf", "birthdate"]);

			// Check if phone already exists (excluding the current user)
			// $query3 = "SELECT phone FROM user WHERE phone = '{$data->phone}' AND uuid != '{$token->uuid}'";
			// $result3 = $this->mysqli->query($query3);

			// if ($result3->num_rows > 0) {
			// 	http_response_code(502);
			// 	echo json_encode(["message" => "Telefone já está cadastrado."]);
			// 	exit();
			// }

            $stmt = $this->mysqli->prepare("UPDATE user SET full_name = ?, cpf = ?, phone = ?, email = ?, birthdate = ?
                                            							WHERE uuid = ?");

			$stmt->bind_param('ssssss', $data->full_name, $data->cpf, $data->phone, $data->email, $data->birthdate, $token->uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Usuário atualizado."]);
				exit();
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar usuário."]);
				exit();
			}
		}

		// "DELETE /v1/user/([\w-]+)"
		public function delete_user($user_uuid) {

			// Check if user with given ID exists
			$checkQuery = "SELECT id FROM user WHERE uuid = ?";
			$checkStmt = $this->mysqli->prepare($checkQuery);
			$checkStmt->bind_param('s', $user_uuid);
			$checkStmt->execute();
			$checkStmt->store_result();

			if ($checkStmt->num_rows === 0) {
				http_response_code(404); // Not Found
				echo json_encode(["message" => "Usuário não encontrado."]);
				exit();
			}

			// User exists, proceed with deactivation
			$deleteQuery = "UPDATE user SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$deleteStmt = $this->mysqli->prepare($deleteQuery);
			$deleteStmt->bind_param('s', $user_uuid);

			if ($deleteStmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Usuário desativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao deletar usuário: " . $this->mysqli->error]);
			}

			$this->mysqli->close();
		}

		// "PUT /v1/user/reactivate/([\w-]+)"
		public function reactivate_user($user_uuid) {
			// Check if user with given ID exists
			$checkQuery = "SELECT id FROM user WHERE uuid = ?";
			$checkStmt = $this->mysqli->prepare($checkQuery);
			$checkStmt->bind_param('s', $user_uuid);
			$checkStmt->execute();
			$checkStmt->store_result();

			if ($checkStmt->num_rows === 0) {
				http_response_code(404); // Not Found
				echo json_encode(["message" => "Usuário não encontrado."]);
				exit();
			}

			// User exists, proceed with reactivation
			$reactivateQuery = "UPDATE user SET deleted_at = NULL WHERE uuid = ?";
			$reactivateStmt = $this->mysqli->prepare($reactivateQuery);
			$reactivateStmt->bind_param('s', $user_uuid);

			if ($reactivateStmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Usuário reativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao reativar usuário: " . $this->mysqli->error]);
			}

			$this->mysqli->close();
		}

		public function upload_profile_picture()
		{
			$token = validate_token();

			$target_dir = "uploads/profile_image/";
			$original_file_name = basename($_FILES["user_image"]["name"]);
			$target_file = $target_dir . $original_file_name;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			if (!$_FILES["user_image"]["tmp_name"]) {
				http_response_code(409);
				echo json_encode(["error" => "Imagem não encontrada."]);
			}

			// // Check if image file is a valid image
			$check = getimagesize($_FILES["user_image"]["tmp_name"]);

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
			if ($_FILES["user_image"]["size"] > 5242880) {
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
				$new_file_name = "profile_img_" . $token->uuid . "." . $imageFileType;
				$new_target_file = $target_dir . $new_file_name;

				if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file)) {
					// Rename the file
					if (rename($target_file, $new_target_file)) {

						// Update user img_url in the database
						$query = "UPDATE user SET profile_picture_url = ? WHERE uuid = ?";
						$stmt = $this->mysqli->prepare($query);
						$stmt->bind_param('ss', $new_target_file, $token->uuid);

						if ($stmt->execute()) {
							echo json_encode(["profile_picture_url" => "./uploads/profile_image/" . $new_file_name]);
						} else {
							echo json_encode(["error" => "Erro ao salvar no banco de dados."]);
						}

						$stmt->close();
						$this->mysqli->close();
					} else {
						echo json_encode(["error" => "Erro ao renomear imagem."]);
					}
				} else {
					echo json_encode(["error" => "Erro no upload."]);
				}
			}
		}

		// public function upload_background_picture()
		// {
		// 	$token = validate_token();

		// 	$target_dir = "uploads/background_image/";
		// 	$original_file_name = basename($_FILES["background_image"]["name"]);
		// 	$target_file = $target_dir . $original_file_name;
		// 	$uploadOk = 1;
		// 	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		// 	if (!$_FILES["background_image"]["tmp_name"]) {
		// 		http_response_code(409);
		// 		echo json_encode(["error" => "Imagem não encontrada."]);
		// 	}

		// 	// // Check if image file is a valid image
		// 	$check = getimagesize($_FILES["background_image"]["tmp_name"]);

		// 	if ($check === false) {
		// 		http_response_code(409);
		// 		echo json_encode(["error" => "Imagem inválida."]);
		// 		$uploadOk = 0;
		// 	}

		// 	// Check if file already exists
		// 	if (file_exists($target_file)) {
		// 		http_response_code(409);
		// 		echo json_encode(["error" => "Imagem duplicada."]);
		// 		$uploadOk = 0;
		// 	}

		// 	// Check file size
		// 	if ($_FILES["background_image"]["size"] > 5242880) {
		// 		http_response_code(409);
		// 		echo json_encode(["error" => "Imagem deve ter menos de 5Mb."]);
		// 		$uploadOk = 0;
		// 	}

		// 	// Allow certain file formats
		// 	$allowed_formats = ["jpg", "jpeg", "png", "gif", "webp"];
		// 	if (!in_array($imageFileType, $allowed_formats)) {
		// 		http_response_code(409);
		// 		echo json_encode(["error" => "Formato não permitido. A imagem deve ser JPG, JPEG, PNG, GIF ou WEBP."]);
		// 		$uploadOk = 0;
		// 	}

		// 	// Check if $uploadOk is set to 0 by an error
		// 	if ($uploadOk == 0) {
		// 		http_response_code(409);
		// 		echo json_encode(["error" => "Erro ao enviar imagem."]);
		// 	} else {
		// 		// Generate new file name
		// 		$new_file_name = "background_image" . $token->uuid . "." . $imageFileType;
		// 		$new_target_file = $target_dir . $new_file_name;

		// 		if (move_uploaded_file($_FILES["background_image"]["tmp_name"], $target_file)) {
		// 			// Rename the file
		// 			if (rename($target_file, $new_target_file)) {

		// 				// Update user img_url in the database
		// 				$query = "UPDATE user SET image_url = ? WHERE uuid = ?";
		// 				$stmt = $this->mysqli->prepare($query);
		// 				$stmt->bind_param('ss', $new_target_file, $token->uuid);

		// 				if ($stmt->execute()) {
		// 					echo json_encode(["background_url" => "./uploads/background_url/" . $new_file_name]);
		// 				} else {
		// 					echo json_encode(["error" => "Erro ao salvar no banco de dados."]);
		// 				}

		// 				 $stmt->close();
		// 				$this->mysqli->close();
		// 			} else {
		// 				echo json_encode(["error" => "Erro ao renomear imagem."]);
		// 			}
		// 		} else {
		// 			echo json_encode(["error" => "Erro no upload."]);
		// 		}
		// 	}
		// }
	}