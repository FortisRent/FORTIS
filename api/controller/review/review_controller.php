<?php
	require_once('./service/utils.php');

	// {
	//     "clinic_uuid": "2kMnrAAWbjvnhxKGdjH6fGpdijWf5qOZ", 
	//     "score": 5,
	//     "description": "Ótimo, fui muito bem atendido."
	// }

	class ReviewController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		public function get_review() {
			$query = "  SELECT r.*, d.id, u.name as doctor_name
								FROM review r
								INNER JOIN appointment_scheduling aps
									ON r.appointment_id = aps.id
								INNER JOIN doctor_calendar dc
									ON aps.doctor_calendar_id = dc.id
								INNER JOIN doctor_clinic d
									ON dc.doctor_id = d.id
								INNER JOIN user u
									ON d.user_id = u.id ";

			$result = $this->mysqli->query($query);

			if ($result) {
				$review = [];
				while ($row = $result->fetch_assoc()) {
					$review[] = $row;
				}
				echo json_encode(["review" => $review]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		public function get_review_by_uuid($review_uuid) {
			$query = "	SELECT r.*, d.id, u.name as doctor_name
								FROM review r
								INNER JOIN appointment_scheduling aps
									ON r.appointment_id = aps.id
								INNER JOIN doctor_calendar dc
									ON aps.doctor_calendar_id = dc.id
								INNER JOIN doctor_clinic d
									ON dc.doctor_id = d.id
								INNER JOIN user u
									ON d.user_id = u.id
								WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $review_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "review not found"]);
				exit();
			}

			$review = $result->fetch_assoc();
			echo json_encode(["review" => $review]);
		}

		public function get_review_by_doctor_uuid($doctor_uuid) {
			$token = validate_token();

			$query = "SELECT r.*, d.id, u.name as doctor_name
								FROM review r
								INNER JOIN appointment_scheduling aps
									ON r.appointment_id = aps.id
								INNER JOIN doctor_calendar dc
									ON aps.doctor_calendar_id = dc.id
								INNER JOIN doctor_clinic d
									ON dc.doctor_id = d.id
								INNER JOIN user u
									ON d.user_id = u.id
								WHERE d.id = (SELECT id from doctor_clinic WHERE uuid = ?)";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $doctor_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$review = [];
			while ($row = $result->fetch_assoc()) {
				$review[] = $row;
			}

			echo json_encode(["reviews" => $review]);
		}

		public function get_doctor_average_score($doctor_uuid) {
			$query = "	SELECT d.id as doctor_id, r.positive_details, r.negative_details, u.name as doctor_name,
									CASE 
										WHEN COUNT(r.id) >= 10 THEN AVG(r.score)
										ELSE 'Novo'
									END AS average_score
								FROM 
									review r
								INNER JOIN appointment_scheduling aps
									ON r.appointment_id = aps.id
								INNER JOIN doctor_calendar dc
									ON aps.doctor_calendar_id = dc.id
								INNER JOIN doctor_clinic d
									ON dc.doctor_id = d.id
								INNER JOIN user u
									ON d.user_id = u.id
								WHERE 
									d.id =(SELECT id FROM doctor_clinic WHERE uuid = ?)";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $doctor_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$avg = $result->fetch_assoc();

			echo json_encode($avg);
		}

		public function get_review_by_logged_user() {
			$token = validate_token();

			$query = "SELECT * FROM review WHERE user_id = (SELECT id FROM appointment_scheduling where user_id = (SELECT id FROM user WHERE  uuid = ?)";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $token->uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$review = [];
			while ($row = $result->fetch_assoc()) {
				$review[] = $row;
			}

			echo json_encode(["reviews" => $review]);
		}

		public function create_review() {
			$token = validate_token();

            $data = validate_payload([ "appointment_uuid", "positive_details", "negative_details", "score"]);

            $uuid = generate_uuid_v3(64);

			$query = "	INSERT INTO review (uuid, user_id, appointment_id, positive_details, negative_details, score)
								SELECT ?, 
										(SELECT id FROM user WHERE uuid = ?), 
										(SELECT id FROM appointment_scheduling WHERE uuid = ?), 
										?, ?, ?
								WHERE NOT EXISTS (
									SELECT 1 
									FROM review 
									WHERE appointment_id = (SELECT id FROM appointment_scheduling WHERE uuid = ?)
										AND user_id = (SELECT id FROM user WHERE uuid = ?)
								);";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('sssssiss', $uuid, $token->uuid, $data->appointment_uuid, $data->positive_details, $data->negative_details, $data->score, $data->appointment_uuid, $token->uuid);


			if ($stmt->execute() && $stmt->affected_rows > 0) {
                http_response_code(201);
                echo json_encode(["message" => "Avaliação cadastrada."]);
			} else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao cadastrar avaliação pois já existe avaliação cadastrada."]);
			}

		}

		public function edit_review($review_uuid) {

			validate_token();

			$data = validate_payload( ["score", "description"]);

			$query = "UPDATE review SET `score` = ?, `description` = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('iss', $data->score, $data->description, $review_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Avaliação atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar avaliação."]);
			}
		}

		public function delete_review($review_uuid) {
			$query = "UPDATE review SET is_active = 0 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $review_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Avaliação desativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao deletar avaliação."]);
			}
		}


		# "PUT /v1/review/reactivate/(\w+)"    
		public function reactivate_review($review_uuid) {
			$query = "UPDATE review SET is_active = 1 WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $review_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Avaliação reativado."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar avaliação."]);
			}
		}
	}