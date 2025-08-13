<?php
    require_once './service/jwt_service.php';

	class LoginController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		public function login() {
            http_response_code(200);
        
            // Step 1: receive email and password
            if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                http_response_code(200);
                echo json_encode(["message" => "Por favor, envie o token."]);
                die();
            }
        
            $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
        
            // Check if the Authorization header starts with 'Basic'
            if (substr($auth_header, 0, 6) !== 'Basic ') {
                http_response_code(401);
                echo json_encode(["message" => "Token inválido."]);
                die();
            }
        
            // Extract and decode the base64-encoded credentials
            $encoded_credentials = substr($auth_header, 6);
            $decoded_credentials = base64_decode($encoded_credentials);
        
            // Split the credentials into username and password
            list($auth_username, $auth_password) = explode(':', $decoded_credentials, 2);
        
            try {
                $query = "SELECT uuid, id, phone, full_name, password FROM user WHERE phone = ?";
                $stmt = $this->mysqli->prepare($query);
            
                if (!$stmt) {
                    http_response_code(500);
                    echo json_encode(["error" => "Failed to prepare statement: " . $this->mysqli->error]);
                    die();
                }
            
                
                $stmt->bind_param("s", $auth_username);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
            
                if ($result) {
                    if (sha1($auth_password) == $result["password"]) {
                        $user = [
                            "id"            => $result['id'],
                            "uuid"          => $result['uuid'],
                            "phone"         => $result['phone'],
                            "full_name"     => $result['full_name']
                        ];
            
                        http_response_code(200);
                        echo json_encode(["access_token" => generate_token($user)]);
                        die();
                    } else {
                        http_response_code(401);
                        echo json_encode(["message" => "Telefone ou senha incorreta."]);
                        die();
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Telefone ou senha incorreta."]);
                }
            
                $stmt->close();
                $this->mysqli->close();
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
                die();
            }
        }
        
        
        function validate_token() {
            if (!isset($_SERVER['HTTP_TOKEN'])) {
                http_response_code(200); 
                echo json_encode(["token" => "Por favor, envie o token."]);
                die();
            }

            $token = $_SERVER['HTTP_TOKEN'];

            http_response_code(200); 
            echo json_encode(["token" => validate_token()]);
        }
	}