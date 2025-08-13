<?php
	require_once('./controller/stripe_master/init.php');
	require_once('./service/utils.php');

	// {
	// 	"name": "Luis Zimermann",
	// 	"amount": 1990,
	// }

	class PlanController {

		private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

		public function get_plan() {
            validate_token();
            try {
                $query = "SELECT * FROM plan";
                $result = $this->mysqli->query($query);
        
                $plans = [];
                while ($row = $result->fetch_assoc()) {
                    $plans[] = $row;
                }
        
                echo json_encode(["plans" => $plans]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }
    
        public function get_plan_by_uuid($plan_uuid) {
            try {
                $query = "SELECT * FROM plan WHERE uuid = ?";
                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $plan_uuid);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $plan = $result->fetch_assoc();
        
                if (!$plan) {
                    http_response_code(404);
                    echo json_encode(["error" => "Plan not found"]);
                    exit();
                }
        
                echo json_encode(["plan" => $plan]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }
        
        public function create_plan()
        {
            $data = json_decode(file_get_contents('php://input'));

            if (!$data) {
                http_response_code(400);
                echo json_encode(["error" => "JSON data is missing or invalid."]);
                exit();
            }

            $requiredFields = ["name", "amount"];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!property_exists($data, $field) || ($field !== 'is_admin' && empty($data->$field))) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                http_response_code(400);
                echo json_encode(["error" => "The following fields are required: " . implode(', ', $missingFields)]);
                exit();
            }

            try {
                // Check if name already exists
                $query3 = "SELECT name FROM plan WHERE name = ?";
                $stmt3 = $this->mysqli->prepare($query3);
                $stmt3->bind_param('s', $data->name);
                $stmt3->execute();
                $stmt3->store_result();

                if ($stmt3->num_rows > 0) {
                    http_response_code(409);
                    echo json_encode(["message" => "Nome já está cadastrado."]);
                    exit();
                }

                $stmt = $this->mysqli->prepare("INSERT INTO plan (uuid, name, amount) 
                                            VALUES (?, ?, ?)");

                $uuid = random_str(32);

                $stmt->bind_param('ssi', $uuid, $data->name, $data->amount);

                if ($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode(["message" => "Plano cadastrado."]);
                    exit();
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erro ao cadastrar plano."]);
                    exit();
                }
            } catch (Exception $e) {
                http_response_code(500);
                return json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		public function update_plan($plan_uuid)
		{
			$data = json_decode(file_get_contents('php://input'));

			if (!$data) {
				http_response_code(400);
				echo json_encode(["error" => "JSON data is missing or invalid."]);
				exit();
			}

			$requiredFields = ["name", "amount"];
			$missingFields = [];

			foreach ($requiredFields as $field) {
				// Check if the property exists and is not null
				if (!property_exists($data, $field) || ($field !== "is_admin" && !isset($data->$field)) || ($field === "is_admin" && $data->$field === null)) {
					$missingFields[] = $field;
				}
			}

			if (!empty($missingFields)) {
				http_response_code(400);
				echo json_encode(["error" => "The following fields are required: " . implode(', ', $missingFields)]);
				exit();
			}

			$stmt = $this->mysqli->prepare("UPDATE plan SET name = ?, amount = ? WHERE uuid = ?");

			$stmt->bind_param('sis', $data->name, $data->amount, $plan_uuid);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Plano atualizado."]);
				exit();
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar plano."]);
				exit();
			}
		}

		public function delete_plan($plan_uuid) {
			// Check if plan with given ID exists
			$check_query = "SELECT id FROM plan WHERE uuid = ?";
			$check_stmt = $this->mysqli->prepare($check_query);
			$check_stmt->bind_param('s', $plan_uuid);
			$check_stmt->execute();
			$check_stmt->store_result();
		
			if ($check_stmt->num_rows === 0) {
				http_response_code(404); // Not Found
				echo json_encode(["message" => "Plano não encontrado."]);
				exit();
			}
		
			// Plan exists, proceed with deactivation
			$delete_query = "UPDATE plan SET is_active = 0 WHERE uuid = ?";
			$delete_stmt = $this->mysqli->prepare($delete_query);
			$delete_stmt->bind_param('s', $plan_uuid);
		
			if ($delete_stmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Plano desativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao deletar plano: " . $this->mysqli->error]);
			}
		
			$this->mysqli->close();
		}
		
		public function reactivate_plan($plan_uuid) {
			// Check if plan with given ID exists
			$check_query = "SELECT id FROM plan WHERE uuid = ?";
			$check_stmt = $this->mysqli->prepare($check_query);
			$check_stmt->bind_param('s', $plan_uuid);
			$check_stmt->execute();
			$check_stmt->store_result();
		
			if ($check_stmt->num_rows === 0) {
				http_response_code(404); // Not Found
				echo json_encode(["message" => "Plano não encontrado."]);
				exit();
			}
		
			// Plan exists, proceed with reactivation
			$reactivate_query = "UPDATE plan SET is_active = 1 WHERE uuid = ?";
			$reactivate_stmt = $this->mysqli->prepare($reactivate_query);
			$reactivate_stmt->bind_param('s', $plan_uuid);
		
			if ($reactivate_stmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Plano reativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao reativar plano: " . $this->mysqli->error]);
			}
		
			$this->mysqli->close();
		}
		
		public function upload_image($plan_uuid)
		{
			$target_dir = "uploads/plan/";
			$original_file_name = basename($_FILES["plan_image"]["name"]);
			$target_file = $target_dir . $original_file_name;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			if (!$_FILES["plan_image"]["tmp_name"]) {
				http_response_code(409);
				echo json_encode(["error" => "Imagem não encontrada."]);
			}

			// Check if image file is a valid image
			$check = getimagesize($_FILES["plan_image"]["tmp_name"]);

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
			if ($_FILES["plan_image"]["size"] > 5242880) {
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
				$new_file_name = "plan_img_" . $plan_uuid . "." . $imageFileType;
				$new_target_file = $target_dir . $new_file_name;

				if (move_uploaded_file($_FILES["plan_image"]["tmp_name"], $target_file)) {
					// Rename the file
					if (rename($target_file, $new_target_file)) {
		
						// Update plan img_url in the database
						$query = "UPDATE plan SET image_url = ? WHERE uuid = ?";
						$stmt = $this->mysqli->prepare($query);
						$stmt->bind_param('ss', $new_file_name, $plan_uuid);

						if ($stmt->execute()) {
							echo json_encode(["message" => "https://api.omnifitness.com.br/uploads/plan/" . $new_file_name]);
							// To view the image: "https://api.fluxfacility.cloud/uploads/plan/{image file name.jpg}
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
	}