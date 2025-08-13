<?php
	require_once('./service/utils.php');

	// {
	//     "name": "Project Gamma",
	//     "description": "This is a description of Project Gamma.",
	//     "machine_category_uuid": "2mN17XbfbDIcx5iO3nMtzTIsDkUZlxs7yIfU1NXGccbPkRumHKtpYy5SrOfCOt1T",
	//     "max_volume": 1500,
	//     "expected_date": "2025-09-15",
	//     "zip_code": "12345-678",
	//     "street": "Maple Street",
	//     "number_street": "456",
	//     "complement": "Suite 12",
	//     "neighborhood": "Downtown",
	//     "city_id": 5,
	//     "state_id": 20
	// }

	class ProjectChecklistController {
		private $mysqli;

		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}
	
		# "GET /v1/project/(\w+)"       
		public function get_project_checklist_by_uuid($project_checklist_uuid) {
			validate_token();

			$query = " SELECT pc.uuid AS checklist_uuid, p.uuid AS project_uuid, pc.title, pc.description, pc.is_verified
								FROM project_checklist pc
								INNER JOIN project p ON pc.project_id = p.id
								WHERE pc.uuid = ? 
								AND pc.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_checklist_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$project_checklist = [];
			while ($row = $result->fetch_assoc()) {
				$project_checklist[] = $row;
			}

			echo json_encode(["project_checklist" => $project_checklist]);
		}

		// "GET /v1/project/checklist/budget/([\w-]+)"
		public function get_project_checklist_by_budget_uuid($budget_uuid) {
			validate_token();

			$query = "  SELECT	pc.uuid AS checklist_uuid, p.uuid AS project_uuid, b.uuid AS budget_uuid, pc.title, pc.description, pc.is_verified
								FROM project_checklist pc
								INNER JOIN project p ON pc.project_id = p.id
								INNER JOIN budget b ON b.project_id = p.id
								WHERE b.uuid = ? AND pc.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $budget_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			$project = [];
			while ($row = $result->fetch_assoc()) {
				$project[] = $row;
			}

			echo json_encode(["project" => $project]);
		}
	
	# "POST /v1/project/checklist" 
	public function create_project_checklist() {
		validate_token();

		$data = validate_payload(["budget_uuid", "title"]);

		$query = "INSERT INTO project_checklist (project_id, title, description) VALUES ((SELECT project_id FROM budget WHERE uuid = ?), ?, ?)";
		$stmt = $this->mysqli->prepare($query);

		$stmt->bind_param('sss', $data->budget_uuid, $data->title, $data->description);

		if ($stmt->execute()) {
			http_response_code(201);
			echo json_encode(["message" => "Checklist do projeto cadastrado com sucesso."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao cadastrar cargo."]);
		}
	}
		
	// DELETE /v1/project/checklist/([\w-]+)
	public function delete_project_checklist($project_checklist_uuid) {
		$query = "UPDATE project_checklist SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $project_checklist_uuid);

		if ($stmt->execute()) {
		http_response_code(200);
		echo json_encode(["message" => "Checklist do projeto desativado."]);
		} else {
		http_response_code(500);
		echo json_encode(["message" => "Erro ao deletar checklist do projeto."]);
		}
	}

	// PUT /v1/project/checklist/([\w-]+)
	public function update_verified_project_checklist($project_checklist_uuid) {
		$query = "UPDATE project_checklist SET is_verified = 1 WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $project_checklist_uuid);

		if ($stmt->execute()) {
		http_response_code(200);
		echo json_encode(["message" => "Item do checklist marcado como confirmado."]);
		} else {
		http_response_code(500);
		echo json_encode(["message" => "Erro ao confirmar item do checklis."]);
		}
	}

		// "PUT /v1/project/checklist/reactivate/(\w+)"
	public function reactivate_project_checklist($project_checklist_uuid) {
		$query = "UPDATE project_checklist SET deleted_at = NULL WHERE uuid = ?";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $project_checklist_uuid);

		if ($stmt->execute()) {
			http_response_code(200);
			echo json_encode(["message" => "Checklist do projeto reativado."]);
		} else {
			http_response_code(500);
			echo json_encode(["message" => "Erro ao reativar projeto."]);
		}
	}

}