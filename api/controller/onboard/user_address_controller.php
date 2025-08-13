<?php
require_once('./service/utils.php');

// {
// 	"zip_code": "23322-310"
// 	"city_id":   "1"
// 	"address":   "Rua joao da silva"
// 	"complement":"apto"
// 	"latitude":  "-27.59189478469467 "
// 	"longitude": "-48.516681716394224"
//	"number":"2"
// }

class UserAddressController
{
	private $mysqli;

	public function __construct($mysqli)
	{
		$this->mysqli = $mysqli;
	}
    # "GET /v1/user/address/" 
	public function get_user_address()
	{
		$query = "SELECT ua.*, c.name as city_name, gs.name as state_name 
							FROM user_address ua
							LEFT JOIN city  c ON ua.city_id = c.id
							LEFT JOIN geo_state gs ON c.state_id = gs.id";

		$result = $this->mysqli->query($query);

		if ($result) {
			$user_address = [];
			while ($row = $result->fetch_assoc()) {
				$user_address[] = $row;
			}
			echo json_encode(["user_address" => $user_address]);
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
		}
	} // ok


    # GET /v1/user/address/(\w+)" 
	public function get_user_address_by_address_uuid($user_uuid)
	{
		$query ="	SELECT ua.*, c.name as city_name, gs.name as state_name 
							FROM user_address ua
							LEFT JOIN city  c ON ua.city_id = c.id
							LEFT JOIN geo_state gs ON c.state_id = gs.id
							WHERE ua.uuid = ? AND ua.deleted_at IS NULL";

		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $user_uuid);
		$stmt->execute();
		$result = $stmt->get_result();

		$user_address = [];

		while ($row = $result->fetch_assoc()) {
			$user_address[] = $row;
		}

		echo json_encode(["user_address" => $user_address]);
	}

    #"GET /v1/user/address/logged/" 
	public function get_user_address_by_user_uuid($user_uuid)
	{
		$query ="SELECT ua.*, c.name AS city_name, gs.name as state_name
                    FROM user_address ua
                    LEFT JOIN city  c ON ua.city_id = c.id
					LEFT JOIN geo_state gs ON c.state_id = gs.id
                    WHERE ua.user_id = (SELECT id FROM user WHERE uuid = ?) AND ua.deleted_at IS NULL";

		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $user_uuid);
		$stmt->execute();
		$result = $stmt->get_result();

		$user_address = [];

		while ($row = $result->fetch_assoc()) {
			$user_address[] = $row;
		}

		echo json_encode(["user_address" => $user_address]);
	}

	  #"GET /v1/user/address/logged/" 
	  public function get_user_address_by_logged(){

		$token = validate_token();

		$query ="	SELECT ua.*, c.name as city_name, gs.name as state_name 
							FROM user_address ua
							LEFT JOIN city  c ON ua.city_id = c.id
							LEFT JOIN geo_state gs ON c.state_id = gs.id
							WHERE ua.user_id = (SELECT id FROM user WHERE uuid = ?) AND ua.deleted_at IS NULL";

		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $token->uuid);
		$stmt->execute();
		$result = $stmt->get_result();

		$user_address = [];

		while ($row = $result->fetch_assoc()) {
			$user_address[] = $row;
		}

		echo json_encode(["user_address" => $user_address]);
	  } 

    # "POST /v1/user/address/"
    public function create_user_address()
    {
        $token = validate_token();

		$data = validate_payload(["zip_code", "city_name", "street", "number_street", "complement", "neighborhood","state_name"]);

        $query = "INSERT INTO user_address (user_id, city_id, zip_code, street, number_street, complement, neighborhood)
						VALUES ((SELECT id FROM user WHERE uuid = ?), (SELECT c.id FROM city c INNER JOIN geo_state gs ON c.state_id = gs.id WHERE c.name = ? AND gs.abbreviation = ?), ?, ?, ?, ?, ?)";
    
        $stmt = $this->mysqli->prepare($query);
    
        
        $stmt->bind_param('ssisssss', $token->uuid, $data->city_name, $data->state_name, $data->zip_code, $data->street, $data->number_street, $data->complement, $data->neighborhood);
    
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Endereço do usuário cadastrado."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao cadastrar endereço.", "error" => mysqli_error($this->mysqli)]);
        }
    }
    
     
    #"PUT /v1/user/address/(\w+)" 
	public function update_user_address($user_address_uuid)
	{
		$data = validate_payload([]);

		$query = "UPDATE user_address SET city_id = (SELECT c.id FROM city c INNER JOIN geo_state gs ON c.state_id = gs.id WHERE c.name = ? AND gs.abbreviation = ?), zip_code = ?, street = ?, number_street = ?, complement = ?, neighborhood = ? WHERE uuid = ? ";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ssisssss', $data->city_name,$data->state_name, $data->zip_code, $data->street, $data->number_street, $data->complement ,$data->neighborhood, $user_address_uuid);

		if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Endereço do usuário atualizado."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao atualizar o endereço."]);
		}
	} 

    #"DELETE /v1/user/address/(\w+)"	
	public function delete_user_address($user_address_uuid)           
	{
		$query = "UPDATE user_address SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $user_address_uuid);

		if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Endereço desativado."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao deletar o endereço."]);
		}
	}

    #"PUT /v1/user/address/reactivate/(\w+)"
	public function reactivate_user_address ($user_address_uuid)
	{
		$query = "UPDATE user_address SET deleted_at = NULL WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $user_address_uuid);

		if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Endereço reativado."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao reativar o endereço."]);
		}
	}
}
