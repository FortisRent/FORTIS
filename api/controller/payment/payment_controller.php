<?php
	// require_once('./controller/stripe_master/init.php');
	require_once('./service/utils.php');

	// {
	// 	"name": "Luis Zimermann",
	// 	"amount": 1990,
	// }

	class PaymentController {

		private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

		public function get_today_payment() {
			$token = validate_token();

            try {
                $query = "SELECT p.uuid, p.charge_id, p.amount, p.payment_method_id, pm.name as payment_name, p.is_verified, p.is_completed, DATE_FORMAT(p.due_date, '%d/%m/%Y') as due_date, 
							  				aps.id as appointment_id, du.name as doctor_name, pu.name as patient_name
								FROM payment p
								INNER JOIN payment_method pm ON p.payment_method_id = pm.id
								INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
								INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
								INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
								INNER JOIN user du ON d.user_id = du.id
								INNER JOIN user pu ON aps.user_id = pu.id
								WHERE du.uuid = ? AND p.is_verified = 1 AND DATE(p.due_date) = CURDATE();";

                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $token->uuid);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $payment = $result->fetch_all(MYSQLI_ASSOC);

				if (!$payment) {
					http_response_code(201);
					echo json_encode(["error" => "Payments of today not found"]);
					exit();
				}
				echo json_encode(["payment" => $payment]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		public function get_payment_thirty_days() {

            $token = validate_token();

            try {
                $query = "SELECT p.uuid, p.charge_id, p.amount, p.payment_method_id, pm.name AS payment_name, p.is_verified, p.is_completed, DATE_FORMAT(p.due_date, '%d/%m/%Y') AS due_date, aps.id AS appointment_id, du.name AS doctor_name, pu.name AS patient_name
									FROM payment p
									INNER JOIN payment_method pm ON p.payment_method_id = pm.id
									INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
									INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
									INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
									INNER JOIN user du ON d.user_id = du.id
									INNER JOIN user pu ON aps.user_id = pu.id
									WHERE du.uuid = ? AND p.is_verified = 1 AND DATE(p.due_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND DATE(p.due_date) <= CURDATE();";

                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $token->uuid);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $payment = $result->fetch_all(MYSQLI_ASSOC);

				if (!$payment) {
					http_response_code(404);
					echo json_encode(["error" => "Payments of 30 days not found"]);
					exit();
				}
				echo json_encode(["payment" => $payment]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		public function get_payment_biweekly_days() {

            $token = validate_token();

            try {
                $query = "SELECT p.uuid, p.charge_id, p.amount, p.payment_method_id, pm.name AS payment_name, p.is_verified, p.is_completed, DATE_FORMAT(p.due_date, '%d/%m/%Y') AS due_date, aps.id AS appointment_id, du.name AS doctor_name, pu.name AS patient_name
									FROM payment p
									INNER JOIN payment_method pm ON p.payment_method_id = pm.id
									INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
									INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
									INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
									INNER JOIN user du ON d.user_id = du.id
									INNER JOIN user pu ON aps.user_id = pu.id
									WHERE du.uuid = ? AND DATE(p.due_date) >= DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND DATE(p.due_date) <= CURDATE();";

                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $token->uuid);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $payment = $result->fetch_all(MYSQLI_ASSOC);

				if (!$payment) {
					http_response_code(404);
					echo json_encode(["error" => "Payments of 15 days not found"]);
					exit();
				}
				echo json_encode(["payment" => $payment]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		public function get_payment_weekly_days() {

            $token = validate_token();

            try {
                $query = "SELECT p.uuid, p.charge_id, p.amount, p.payment_method_id, pm.name AS payment_name, p.is_verified, p.is_completed, DATE_FORMAT(p.due_date, '%d/%m/%Y') AS due_date, aps.id AS appointment_id, du.name AS doctor_name, pu.name AS patient_name
									FROM payment p
									INNER JOIN payment_method pm ON p.payment_method_id = pm.id
									INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
									INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
									INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
									INNER JOIN user du ON d.user_id = du.id
									INNER JOIN user pu ON aps.user_id = pu.id
									WHERE du.uuid = ? AND p.is_verified = 1 AND DATE(p.due_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE(p.due_date) <= CURDATE();";

                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $token->uuid);
                $stmt->execute();
                $result = $stmt->get_result();
        
				$payment = $result->fetch_all(MYSQLI_ASSOC);

				if (!$payment) {
					http_response_code(404);
					echo json_encode(["error" => "Payments of 7 days not found"]);
					exit();
				}
				echo json_encode(["payment" => $payment]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }
    
        public function get_payment_by_uuid($payment_uuid) {
            try {
                $query = "SELECT * FROM payment WHERE uuid = ?";
                $stmt = $this->mysqli->prepare($query);
                $stmt->bind_param('s', $payment_uuid);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $payment = $result->fetch_assoc();
        
                if (!$payment) {
                    http_response_code(404);
                    echo json_encode(["error" => "Payment not found"]);
                    exit();
                }
        
                echo json_encode(["payment" => $payment]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }
        
        public function create_payment()
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
                $query3 = "SELECT name FROM payment WHERE name = ?";
                $stmt3 = $this->mysqli->prepare($query3);
                $stmt3->bind_param('s', $data->name);
                $stmt3->execute();
                $stmt3->store_result();

                if ($stmt3->num_rows > 0) {
                    http_response_code(409);
                    echo json_encode(["message" => "Nome já está cadastrado."]);
                    exit();
                }

                $stmt = $this->mysqli->prepare("INSERT INTO payment (uuid, name, amount) 
                                            VALUES (?, ?, ?)");

                $uuid = random_str(32);

                $stmt->bind_param('ssi', $uuid, $data->name, $data->amount);

                if ($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode(["message" => "Plano cadastrado."]);
                    exit();
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erro ao cadastrar paymento."]);
                    exit();
                }
            } catch (Exception $e) {
                http_response_code(500);
                return json_encode(["error" => "Database error: " . $this->mysqli->error]);
            }
        }

		public function update_payment($payment_uuid)
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

			$stmt = $this->mysqli->prepare("UPDATE payment SET name = ?, amount = ? WHERE uuid = ?");

			$stmt->bind_param('sis', $data->name, $data->amount, $payment_uuid);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Plano atualizado."]);
				exit();
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar paymento."]);
				exit();
			}
		}

		public function delete_payment($payment_uuid) {
			// Check if payment with given ID exists
			$check_query = "SELECT id FROM payment WHERE uuid = ?";
			$check_stmt = $this->mysqli->prepare($check_query);
			$check_stmt->bind_param('s', $payment_uuid);
			$check_stmt->execute();
			$check_stmt->store_result();
		
			if ($check_stmt->num_rows === 0) {
				http_response_code(404); // Not Found
				echo json_encode(["message" => "Plano não encontrado."]);
				exit();
			}
		
			// Plan exists, proceed with deactivation
			$delete_query = "UPDATE payment SET is_active = 0 WHERE uuid = ?";
			$delete_stmt = $this->mysqli->prepare($delete_query);
			$delete_stmt->bind_param('s', $payment_uuid);
		
			if ($delete_stmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Plano desativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao deletar paymento: " . $this->mysqli->error]);
			}
		
			$this->mysqli->close();
		}
		
		public function reactivate_payment($payment_uuid) {
			// Check if payment with given ID exists
			$check_query = "SELECT id FROM payment WHERE uuid = ?";
			$check_stmt = $this->mysqli->prepare($check_query);
			$check_stmt->bind_param('s', $payment_uuid);
			$check_stmt->execute();
			$check_stmt->store_result();
		
			if ($check_stmt->num_rows === 0) {
				http_response_code(404); // Not Found
				echo json_encode(["message" => "Plano não encontrado."]);
				exit();
			}
		
			// Plan exists, proceed with reactivation
			$reactivate_query = "UPDATE payment SET is_active = 1 WHERE uuid = ?";
			$reactivate_stmt = $this->mysqli->prepare($reactivate_query);
			$reactivate_stmt->bind_param('s', $payment_uuid);
		
			if ($reactivate_stmt->execute()) {
				http_response_code(200); // OK
				echo json_encode(["message" => "Plano reativado."]);
			} else {
				http_response_code(500); // Internal Server Error
				echo json_encode(["message" => "Erro ao reativar paymento: " . $this->mysqli->error]);
			}
		
			$this->mysqli->close();
		}
		
		// public function upload_image($payment_uuid)
		// {
		// 	$target_dir = "uploads/payment/";
		// 	$original_file_name = basename($_FILES["payment_image"]["name"]);
		// 	$target_file = $target_dir . $original_file_name;
		// 	$uploadOk = 1;
		// 	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		// 	if (!$_FILES["payment_image"]["tmp_name"]) {
		// 		http_response_code(409);
		// 		echo json_encode(["error" => "Imagem não encontrada."]);
		// 	}

		// 	// Check if image file is a valid image
		// 	$check = getimagesize($_FILES["payment_image"]["tmp_name"]);

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
		// 	if ($_FILES["payment_image"]["size"] > 5242880) {
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
		// 		$new_file_name = "payment_img_" . $payment_uuid . "." . $imageFileType;
		// 		$new_target_file = $target_dir . $new_file_name;

		// 		if (move_uploaded_file($_FILES["payment_image"]["tmp_name"], $target_file)) {
		// 			// Rename the file
		// 			if (rename($target_file, $new_target_file)) {
		
		// 				// Update payment img_url in the database
		// 				$query = "UPDATE payment SET image_url = ? WHERE uuid = ?";
		// 				$stmt = $this->mysqli->prepare($query);
		// 				$stmt->bind_param('ss', $new_file_name, $payment_uuid);

		// 				if ($stmt->execute()) {
		// 					echo json_encode(["message" => "https://api.omnifitness.com.br/uploads/payment/" . $new_file_name]);
		// 					// To view the image: "https://api.fluxfacility.cloud/uploads/payment/{image file name.jpg}
		// 				} else {
		// 					echo json_encode(["error" => "Erro ao salvar no banco de dados."]);
		// 				}

		// 				$stmt->close();
		// 				$this->mysqli->close();
		// 			} else {
		// 				echo json_encode(["error" => "Erro ao renomear imagem."]);
		// 			}
		// 		} else {
		// 			echo json_encode(["error" => "Erro no upload."]);
		// 		}
		// 	}
		// }

		public function get_payment_summary_by_doctor_uuid() {

			$token = validate_token();
			
			// Extrato de pagamentos das consultas
			$query1 = "SELECT p.charge_id, p.amount, p.payment_method_id, pm.name as payment_name, p.is_verified, p.is_completed, DATE_FORMAT(p.due_date, '%d/%m/%Y') as due_date, 
							  				aps.id as appointment_id, du.name as doctor_name, pu.name as patient_name
								FROM payment p
								INNER JOIN payment_method pm ON p.payment_method_id = pm.id
								INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
								INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
								INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
								INNER JOIN user du ON d.user_id = du.id
								INNER JOIN user pu ON aps.user_id = pu.id
								WHERE du.uuid = ? AND p.is_verified = 1
								ORDER BY p.id DESC LIMIT 10;";
			
			$result1 = $this->mysqli->prepare($query1);
			$result1->bind_param("s", $token->uuid);
		
			if (!$result1->execute()) {
				http_response_code(500);
				echo json_encode(['message' => "Erro ao buscar detalhes dos pagamentos: " . $this->mysqli->error]);
				return;
			}
		
			$result1_res = $result1->get_result();
			$statement = $result1_res->fetch_all(MYSQLI_ASSOC);
		
			// Soma total de todos os pagamentos verificados
			$query_sum_verified = "SELECT SUM(p.amount) AS total_amount_verified 
													FROM payment p
                                                    INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
													INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
													INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
													INNER JOIN user du ON d.user_id = du.id
													WHERE du.uuid = ? AND p.is_verified = 1;";
			
			$result_sum_verified = $this->mysqli->prepare($query_sum_verified);
			$result_sum_verified->bind_param("s", $token->uuid);
			$result_sum_verified->execute();
			$total_amount_verified = $result_sum_verified->get_result()->fetch_assoc()['total_amount_verified'] ?? 0;
		
			// Soma dos pagamentos verificados de hoje
			$query_sum_today_sales = "SELECT SUM(p.amount) AS total_sales_today 
															FROM payment p
															INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
															INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
															INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
															INNER JOIN user du ON d.user_id = du.id
															WHERE du.uuid = ? AND p.is_verified = 1 AND DATE(p.due_date) = CURDATE();";

			$result_sum_today_sales = $this->mysqli->prepare($query_sum_today_sales);
			$result_sum_today_sales->bind_param("s", $token->uuid);
			$result_sum_today_sales->execute();
			$total_sales_today = $result_sum_today_sales->get_result()->fetch_assoc()['total_sales_today'] ?? 0;

			// Soma das consultas com pagamentos verificados
			$query_sum_appointments = "SELECT COUNT(aps.id) AS total_appointments
															FROM payment p
															INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
															INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
															INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
															INNER JOIN user du ON d.user_id = du.id
															WHERE du.uuid = ? AND p.is_verified = 1;";

			$result_sum_appointments = $this->mysqli->prepare($query_sum_appointments);
			$result_sum_appointments->bind_param("s", $token->uuid);
			$result_sum_appointments->execute();
			$total_appointments = $result_sum_appointments->get_result()->fetch_assoc()['total_appointments'] ?? 0;
		
			// Soma dos valores verificados por mês
			$query_monthly_sums = "SELECT DATE_FORMAT(p.due_date, '%M') AS month, DATE_FORMAT(p.due_date, '%Y') AS year, SUM(p.amount) AS total_amount_monthly
														FROM payment p
														INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
														INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
														INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
														INNER JOIN user du ON d.user_id = du.id
														WHERE du.uuid = ? AND p.is_verified
														GROUP BY YEAR(p.due_date), MONTH(p.due_date), DATE_FORMAT(p.due_date, '%M')
														ORDER BY YEAR(p.due_date), MONTH(p.due_date);";
			
			$result_monthly_sums = $this->mysqli->prepare($query_monthly_sums);
			$result_monthly_sums->bind_param("s", $token->uuid);
			$result_monthly_sums->execute();
			$monthly_sums = $result_monthly_sums->get_result()->fetch_all(MYSQLI_ASSOC);

			// Consultas com pagamento pix
			$query_pix_payments = "	SELECT COUNT(p.payment_method_id) as payment_method_pix
														FROM payment p
														INNER JOIN payment_method pm ON p.payment_method_id = pm.id
														INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
														INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
														INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
														INNER JOIN user du ON d.user_id = du.id
														INNER JOIN user pu ON aps.user_id = pu.id
														WHERE du.uuid = ? AND pm.id = 4;";
			
			$result_pix_payments = $this->mysqli->prepare($query_pix_payments);
			$result_pix_payments->bind_param("s", $token->uuid);
			$result_pix_payments->execute();
			$total_pix_payments = $result_pix_payments->get_result()->fetch_assoc();

			// Consultas com pagamento no cartão
			$query_card_payments ="SELECT COUNT(p.payment_method_id) as payment_method_card
														FROM payment p
														INNER JOIN payment_method pm ON p.payment_method_id = pm.id
														INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
														INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
														INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
														INNER JOIN user du ON d.user_id = du.id
														INNER JOIN user pu ON aps.user_id = pu.id
														WHERE du.uuid = ? AND (pm.id = 2 OR pm.id = 3);";
			
			$result_card_payments = $this->mysqli->prepare($query_card_payments);
			$result_card_payments->bind_param("s", $token->uuid);
			$result_card_payments->execute();
			$total_card_payments = $result_card_payments->get_result()->fetch_assoc();

		
			// Montar resposta final
			$response = [
				'statement' => $statement,
				'summary' => [
					'total_amount_verified' => $total_amount_verified,
					'total_amount_today' => $total_sales_today,
					'total_appointments' => $total_appointments,
					'monthly_sums' => $monthly_sums,
					'pix_payments' => $total_pix_payments,
					'card_payments' => $total_card_payments,
				]
			];
		
			http_response_code(200);
			echo json_encode($response);
		}

		public function get_payment_financial_by_doctor_uuid() {

			$token = validate_token();

			// Pegar os dados do médico
			$query_doctor = "SELECT  du.name as doctor_name, du.cpf as doctor_cpf
											FROM payment p
											INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
											INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
											INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
											INNER JOIN user du ON d.user_id = du.id
											WHERE du.uuid = ? AND p.is_verified = 1;";

			$result_doctor = $this->mysqli->prepare($query_doctor);
			$result_doctor->bind_param("s", $token->uuid);
			$result_doctor->execute();
			$doctor_data = $result_doctor->get_result()->fetch_assoc();
			
			// Extrato de pagamentos das consultas
			$query1 = "SELECT p.charge_id, p.amount, p.payment_method_id, pm.name as payment_name, p.is_verified, p.is_completed, DATE_FORMAT(p.due_date, '%d/%m/%Y') as due_date, 
							  				aps.id as appointment_id, du.name as doctor_name, du.cpf as doctor_cpf, pu.name as patient_name
								FROM payment p
								INNER JOIN payment_method pm ON p.payment_method_id = pm.id
								INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
								INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
								INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
								INNER JOIN user du ON d.user_id = du.id
								INNER JOIN user pu ON aps.user_id = pu.id
								WHERE du.uuid = ? AND p.is_verified = 1 AND DATE(p.due_date) = CURDATE();";
			
			$result1 = $this->mysqli->prepare($query1);
			$result1->bind_param("s", $token->uuid);
		
			if (!$result1->execute()) {
				http_response_code(500);
				echo json_encode(['message' => "Erro ao buscar detalhes dos pagamentos: " . $this->mysqli->error]);
				return;
			}
		
			$result1_res = $result1->get_result();
			$statement = $result1_res->fetch_all(MYSQLI_ASSOC);
		
			// Soma total de todos os pagamentos verificados
			$query_sum_verified = "SELECT SUM(p.amount) AS total_amount_verified 
													FROM payment p
                                                    INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
													INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
													INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
													INNER JOIN user du ON d.user_id = du.id
													WHERE du.uuid = ? AND p.is_verified = 1;";
			
			$result_sum_verified = $this->mysqli->prepare($query_sum_verified);
			$result_sum_verified->bind_param("s", $token->uuid);
			$result_sum_verified->execute();
			$total_amount_verified = $result_sum_verified->get_result()->fetch_assoc()['total_amount_verified'] ?? 0;
		
			// Soma dos pagamentos verificados de hoje
			$query_sum_today_sales = "SELECT SUM(p.amount) AS total_sales_not_completed 
															FROM payment p
															INNER JOIN appointment_scheduling aps ON p.appointment_scheduling_id = aps.id
															INNER JOIN doctor_calendar dc ON aps.doctor_calendar_id = dc.id
															INNER JOIN doctor_clinic d ON dc.doctor_id = d.id
															INNER JOIN user du ON d.user_id = du.id
															WHERE du.uuid = ? AND p.is_verified = 1 AND p.is_completed = 0;";

			$result_sum_today_sales = $this->mysqli->prepare($query_sum_today_sales);
			$result_sum_today_sales->bind_param("s", $token->uuid);
			$result_sum_today_sales->execute();
			$total_sales_not_completed = $result_sum_today_sales->get_result()->fetch_assoc()['total_sales_not_completed'] ?? 0;

		
			// Montar resposta final
			$response = [
				'doctor' => $doctor_data,
				'statement' => $statement,
				'summary' => [
					'total_amount_verified' => $total_amount_verified,
					'total_amount_not_completed' => $total_sales_not_completed,
				]
			];
		
			http_response_code(200);
			echo json_encode($response);
		}
	}