<?php
require_once('./service/utils.php');

// {
// 	"state_id": 15,
// 	"name": "Joana"
// 	"phone": "(48) 9 9999-9999"
// 	"email": "email_teste@teste.com"
// 	"is_doctor": false,
// 	"crm":  "12345"
// }

class LeadController
{
	private $mysqli;

	public function __construct($mysqli)
	{
		$this->mysqli = $mysqli;
	}

    public function get_lead()
	{
        validate_token();

		$query = "SELECT * FROM leads";

		$result = $this->mysqli->query($query);

		if ($result) {
			$lead = [];
			while ($row = $result->fetch_assoc()) {
				$lead[] = $row;
			}
			echo json_encode(["leads" => $lead]);
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
		}
	} // ok


    public function create_lead()
    {
        $data = validate_payload(["state_name", "name", "phone", "email"]);
    
        $uuid = generate_uuid_v3(64);
    
        $query = "INSERT INTO leads (`uuid`, `state_id`,`name`,`phone`,`email`,`is_doctor`,`crm`, `rqe`,`specialty_name`)
						VALUES (?, (SELECT id from geo_state WHERE name = ?), ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->mysqli->prepare($query);
    
        $state_name = trim($data->state_name);
		
		$crm = isset($data->crm) ? $data->crm : null;
		$rqe = isset($data->rqe) ? $data->rqe : null;
		$specialty_name = isset($data->specialty_name) ? $data->specialty_name : null;
		
        $stmt->bind_param('sssssisss', $uuid, $state_name, $data->name, $data->phone, $data->email, $data->is_doctor, $crm, $rqe, $specialty_name);
    
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Inscrição cadastrada."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao cadastrar inscrição.", "error" => mysqli_error($this->mysqli)]);
        }
    }
    
     
	public function update_lead($lead_uuid)
	{
		$data = validate_payload(required_fields: ["name", "phone", "email"]);

		$query = "UPDATE leads SET `name` = ?, `phone` = ?, `email` = ? WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ssss', $data->name, $data->phone, $data->email, $lead_uuid);

		if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Inscrição atualizada."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao atualizar o inscrição."]);
		}
	} 

	public function delete_lead($lead_uuid)           
	{
		$query = "UPDATE leads SET is_active = 0 WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $lead_uuid);

		if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Inscrição desativada."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao deletar a inscrição."]);
		}
	}

	public function reactivate_lead ($lead_uuid)
	{
		$query = "UPDATE leads SET is_active = 1 WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $lead_uuid);

		if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Inscrição reativada."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao reativar a inscrição."]);
		}
	}
}
