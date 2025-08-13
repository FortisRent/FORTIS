<?php
	require_once('./service/utils.php');

    // {
    //     "email": "capivara@gmail.com",
    //     "company_uuid": "8QAGQyJpwPjIsNYBETybuV7qCbnZOajF1GnHooqAH1KziLBFJOmTwoVyNbJMaJJP",
    //     "role_name": "Financeiro"
    // }

	class EmployeeCertificationController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/employee/certification/" 
		public function get_all_employee_certification() {
			$query = "	SELECT ec.uuid, ec.name AS certification_name, ec.file_url, ec.details, e.uuid AS employee_uuid, u.full_name AS employee_name, c.name AS company_name, r.name AS role_name,
                                                DATE_FORMAT(ec.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee_certification ec
                                INNER JOIN employee e ON ec.employee_id = e.id
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
                                WHERE ec.deleted_at IS NULL; ";

			$result = $this->mysqli->query($query);

			if ($result) {
				$employee = [];
				while ($row = $result->fetch_assoc()) {
					$employee[] = $row;
				}
				echo json_encode(["employee" => $employee]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        // "GET /v1/employee/certification/([\w-]+)" 
		public function get_certification_by_employee_uuid($employee_uuid) {

			$query = "	SELECT ec.uuid, ec.name AS certification_name, ec.file_url, ec.details, e.uuid AS employee_uuid, u.full_name AS employee_name, c.name AS company_name, r.name AS role_name,
                                                DATE_FORMAT(ec.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee_certification ec
                                INNER JOIN employee e ON ec.employee_id = e.id
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
								WHERE e.uuid = ? AND ec.deleted_at IS NULL;";


			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$employee = [];

			while ($row = $result->fetch_assoc()) {
				$employee[] = $row;
			}

			echo json_encode(["employees" => $employee]);
		}

        // "GET /v1/certification/"
		public function get_certification_by_uuid($certification_uuid) {

			$query = "	SELECT ec.uuid, ec.name AS certification_name, ec.file_url, ec.details, e.uuid AS employee_uuid, u.full_name AS employee_name, c.name AS company_name, r.name AS role_name,
                                                DATE_FORMAT(ec.created_at, '%d/%m/%Y %H:%i') as created_at
								FROM employee_certification ec
                                INNER JOIN employee e ON ec.employee_id = e.id
								INNER JOIN user u ON e.user_id = u.id
								INNER JOIN company c ON e.company_id = c.id
                                INNER JOIN role r ON e.role_id = r.id
								WHERE ec.uuid = ? AND ec.deleted_at IS NULL";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $certification_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "Funcionário não encontrado"]);
				exit();
			}

			$employee = $result->fetch_assoc();
			echo json_encode(["employee" => $employee]);
		}

		// "POST /v1/employee/certification/"
		public function create_employee_certification() {
			$token = validate_token();

			$data = validate_payload(["employee_uuid" ,"name", "file_url"]);

			$query = "INSERT INTO employee_certification (employee_id, name, file_url, details)
						VALUES ((SELECT id FROM employee WHERE uuid = ?), ?, ?, ?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ssss', $data->employee_uuid, $data->name, $data->file_url, $data->details);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Certificado cadastrado com sucesso."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar certificado."]);
			}
		}

        // "PUT /v1/employee/certification/([\w-]+)"
        public function update_employee_certification($employee_certification_uuid) {

			validate_token();

			$data = validate_payload( ["name", "file_url", "details"]);

			$query = "UPDATE employee_certification SET name = ?, file_url = ?, details = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ssss', $data->name, $data->file_url, $data->details, $employee_certification_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Certificação atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar certificação."]);
			}
		}

        // "DELETE /v1/employee/certification/([\w-]+)"
		public function delete_employee_certification($employee_certification_uuid) {
			$query = "UPDATE employee_certification SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_certification_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Certificado desativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar certificado."]);
			}
		}

        // "PUT /v1/employee/certification/reactivate/([\w-]+)"
		public function reactivate_employee_certification($employee_certification_uuid) {
			$query = "UPDATE employee_certification SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $employee_certification_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Funcionário reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar funcionário."]);
			}
		}

        // "POST /v1/certification/upload/([\w-]+)" 
        public function upload_certification_file($employee_uuid)
		{
            $certification_uuid = generate_uuid_v3(16);

            $query = "SELECT id FROM employee WHERE uuid = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param('s', $employee_uuid);
            $stmt->execute();
            $result = $stmt->get_result();

            $employee_id = $result->fetch_assoc()['id'];

			$target_dir = "uploads/certification/";
			$original_file_name = basename($_FILES["certification_file"]["name"]);
			$target_file = $target_dir . $original_file_name;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			if (!$_FILES["certification_file"]["tmp_name"]) {
				http_response_code(409);
				echo json_encode(["error" => "Arquivo não encontrado."]);
			}

			// Check if image file is a valid image
			// $check = getimagesize($_FILES["certification_file"]["tmp_name"]);

			// if ($check === false) {
			// 	http_response_code(409);
			// 	echo json_encode(["error" => "Imagem inválida."]);
			// 	$uploadOk = 0;
			// }

			// Check if file already exists
			if (file_exists($target_file)) {
				http_response_code(409);
				echo json_encode(["error" => "Arquivo duplicado."]);
				$uploadOk = 0;
			}

			// Check file size
			if ($_FILES["certification_file"]["size"] > 5242880) {
				http_response_code(409);
				echo json_encode(["error" => "Arquivo deve ter menos de 5Mb."]);
				$uploadOk = 0;
			}

			// Allow certain file formats
			$allowed_formats = ["pdf", "jpg", "jpeg", "png", "gif", "webp"];
			if (!in_array($imageFileType, $allowed_formats)) {
				http_response_code(409);
				echo json_encode(["error" => "Formato não permitido. O arquivo deve ser PDF, JPG, JPEG, PNG, GIF ou WEBP."]);
				$uploadOk = 0;
			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				http_response_code(409);
				echo json_encode(["error" => "Erro ao enviar arquivo."]);
			} else {
				// Generate new file name
				$new_file_name = $employee_id . '_' . $certification_uuid . "." . $imageFileType;
				$new_target_file = $target_dir . $new_file_name;

				if (move_uploaded_file($_FILES["certification_file"]["tmp_name"], $target_file)) {
					// Rename the file
					if (rename($target_file, $new_target_file)) {

						// Update user img_url in the database
						// $query = "UPDATE user SET image_url = ? WHERE uuid = ?";
						// $stmt = $this->mysqli->prepare($query);
						// $stmt->bind_param('ss', $new_file_name, $certification_uuid);

						// if ($stmt->execute()) {
							echo json_encode(["file_url" => "/uploads/certification/" . $new_file_name]);
							
						// } else {
							// echo json_encode(["error" => "Erro ao salvar no banco de dados."]);
						// }

						// $stmt->close();
						$this->mysqli->close();
					} else {
						echo json_encode(["error" => "Erro ao renomear arquivo."]);
					}
				} else {
					echo json_encode(["error" => "Erro no upload."]);
				}
			}
		}

	}