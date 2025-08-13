<?php
	require_once './controller/password/token_generator.php';

	class PasswordController {

		private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

		// Recebe o email, valida e envia o código.
		// {
		// 	"email":"luis@55technology.com"
		// }

		public function validate_email() {
			$data = json_decode(file_get_contents('php://input'));
		
			if (!$data) {
				http_response_code(400);
				echo json_encode(["error" => "JSON data is missing or invalid."]);
				exit();
			}
		
			$requiredFields = ["email"];
			$missingFields = [];
		
			foreach ($requiredFields as $field) {
				if (!property_exists($data, $field) || empty($data->$field)) {
					$missingFields[] = $field;
				}
			}
		
			if (!empty($missingFields)) {
				http_response_code(400);
				echo json_encode(["error" => "The following fields are required: " . implode(', ', $missingFields)]);
				exit();
			}
		
			$query = "SELECT password, id FROM user WHERE email = ?";
			$stmt = $this->mysqli->prepare($query);
		
			if (!$stmt) {
				http_response_code(500);
				echo json_encode(["error" => "Failed to prepare statement: " . $this->mysqli->error]);
				exit();
			}
		
			$stmt->bind_param("s", $data->email);
			$stmt->execute();
			$result = $stmt->get_result()->fetch_assoc();
		
			if ($result) {
				$code = recover_code();
				$user_id = $result['id'];
		
				$queryInsert = "INSERT INTO pass_recover_token (code, user_id) VALUES (?, ?)";
				$stmtInsert = $this->mysqli->prepare($queryInsert);
		
				if (!$stmtInsert) {
					http_response_code(500);
					echo json_encode(["error" => "Failed to prepare statement: " . $this->mysqli->error]);
					exit();
				}
		
				$stmtInsert->bind_param("si", $code, $user_id);
				
				if ($stmtInsert->execute()) {
					$to = $data->email;
					$subject = "meetmed - Recuperar senha";
					$message = "Código recuperação de senha: $code.";
					$headers = "From: suporte@meetmed.com" . "\r\n" .
						"Reply-To: $data->email" . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
		
					if (mail($to, $subject, $message, $headers)) {
						http_response_code(200);
						echo json_encode(["message" => "Sucesso! Um código de recuperação de senha foi enviado para o seu email."]);
					} else {
						var_dump(value: $data);
						http_response_code(500);
						echo json_encode(["message" => "Erro ao enviar email. Tente novamente."]);
					}
				} else {
					http_response_code(500);
					echo json_encode(["message" => "Erro ao gerar código de recuperação. Tente novamente."]);
				}
			} else {
				http_response_code(401);
				echo json_encode(["message" => "Email não encontrado"]);
			}
		
			$stmt->close();
			$stmtInsert->close();
		}
		
		// Recebe o código, valida e manda pra próxima página.
		// {
		// 	"code":"lhrAZQ"
		// }
		public function validate_code() {

			$data = validate_payload(["code"]);
		
			$query = "SELECT user.id, tk.code
					FROM pass_recover_token tk
					INNER JOIN user
					ON tk.user_id = user.id
					WHERE tk.code = ?";
		
			$stmt = $this->mysqli->prepare($query);
		
			if (!$stmt) {
				http_response_code(500);
				echo json_encode(["error" => "Failed to prepare statement: " . $this->mysqli->error]);
				exit();
			}
		
			$stmt->bind_param("s", $data->code);
			$stmt->execute();
			$result = $stmt->get_result()->fetch_assoc();
		
			if ($result) {
				if ($data->code == $result['code']) {
					http_response_code(200);
					echo json_encode(["message" => "Código válido. Por favor, atualize sua senha."]);
				} else {
					http_response_code(401);
					echo json_encode(["message" => "Código inválido."]);
				}
			} else {
				http_response_code(401);
				echo json_encode(["message" => "Código inválido."]);
			}
		
			$stmt->close();
		}

		// Atualiza a senha

		// {
		// 	"password":"12345",
		// 	"phone":"(48) 99999-9999"
		// }

		public function update_password() {
		
			$data = validate_payload(["password", "phone"]);
			$querySelect = "SELECT password, id FROM user WHERE phone = ?";
			$stmtSelect = $this->mysqli->prepare($querySelect);
		
			if (!$stmtSelect) {
				http_response_code(500);
				echo json_encode(["error" => "Failed to prepare statement: " . $this->mysqli->error]);
				exit();
			}
		
			$stmtSelect->bind_param("s", $data->phone);
			$stmtSelect->execute();
			$result = $stmtSelect->get_result()->fetch_assoc();
		
			if ($result) {
				$queryUpdate = "UPDATE user SET password = ? WHERE phone = ?";
				$stmtUpdate = $this->mysqli->prepare($queryUpdate);
		
				if (!$stmtUpdate) {
					http_response_code(500);
					echo json_encode(["error" => "Failed to prepare statement: " . $this->mysqli->error]);
					exit();
				}
		
				$encrypted_pass = sha1($data->password);
		
				$stmtUpdate->bind_param("ss", $encrypted_pass, $data->phone);
				if ($stmtUpdate->execute()) {
					http_response_code(200);
					echo json_encode(["message" => "Senha atualizada com sucesso."]);
					exit();
				}
		
				http_response_code(401);
				echo json_encode(["message" => "Falha ao editar senha."]);
				exit();
			} else {
				http_response_code(401);
				echo json_encode(["message" => "Telefone não encontrado"]);
				exit();
			}
		}			
	}
?>