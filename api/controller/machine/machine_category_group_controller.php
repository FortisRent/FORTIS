<?php
	require_once('./service/utils.php');

	// {
	// 	"name": "Escavadeira",
	// 	"description": ""
	// }

	class MachineCategoryGroupController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/machine/category/"
		public function get_all_machine_category_group() {
			$query = " SELECT mcp.id, mcp.uuid, mcp.name as group_name
								FROM machine_category_group mcp;";

			$result = $this->mysqli->query($query);

			if ($result) {
				$machine_category_groups = [];
				while ($row = $result->fetch_assoc()) {
					$machine_category_groups[] = $row;
				}
				echo json_encode(["machine_category_groups" => $machine_category_groups]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        // "GET /v1/machine/category/(\w+)"
		public function get_machine_category_group_by_uuid($machine_category_group_uuid) {
			$query = "  SELECT mcp.uuid, mcp.name as category_name, mcp.description as category_description
								FROM machine_category_group mcp
                                WHERE mcp.uuid = ?;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_category_group_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "machine_category_group not found"]);
				exit();
			}

			$machine_category_group = $result->fetch_assoc();
			echo json_encode(["machine_category_group" => $machine_category_group]);
		}

		 // "GET /v1/machine/category/group/(\w+)"
		 public function get_machine_category_group_by_project_category_uuid($project_category_uuid) {
			$query = " SELECT mcp.uuid, mcp.name as category_name
								FROM machine_category_group mcp
								INNER JOIN project_category pc ON mcp.project_category_id = pc.id
                                WHERE pc.uuid = ? AND mcp.deleted_at IS NULL;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_category_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result) {
				$machine_category_group = [];
				while ($row = $result->fetch_assoc()) {
					$machine_category_group[] = $row;
				}
				http_response_code(200);
				echo json_encode(["machine_category_group" => $machine_category_group]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        // "POST /v1/machine/category/"
		public function create_machine_category_group() {
			$token = validate_token();

			$data = validate_payload(["project_category_name", "name"]);

			$query = "INSERT INTO machine_category_group (project_category_id, name)
						VALUES ((SELECT id FROM project_category WHERE name = ?), ?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('ss', $data->project_category_name, $data->name);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Grupo de categoria do equipamento cadastrada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar grupo categoria do equipamento."]);
			}
		}

        // "PUT /v1/machine/category/(\w+)"
		public function update_machine_category_group($machine_category_group_uuid) {

			validate_token();

			$data = validate_payload( ["name"]);

			$query = "UPDATE machine_category_group SET name = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ss', $data->name, $machine_category_group_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Grupo da categoria do equipamento atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar grupo da categoria do equipamento."]);
			}
		}

        // "DELETE /v1/machine/category/(\w+)"
		public function delete_machine_category_group($machine_uuid) {
			$query = "UPDATE machine_category_group SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Grupo da categoria desativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desativar grupo da categoria."]);
			}
		}

          // "PUT /v1/machine/category/reactivate/(\w+)"
		public function reactivate_machine_category_group($machine_uuid) {
			$query = "UPDATE machine_category_group SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Grupo da categora reativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar grupo da categoria."]);
			}
		}

	}