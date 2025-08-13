<?php
	require_once('./service/utils.php');

	// {
	// 	"name": "Escavadeira",
	// 	"description": ""
	// }

	class ProjectCategoryController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/project/category/"
		public function get_all_project_categories() {
			$query = " SELECT pc.uuid, pc.name as project_category_name
								FROM project_category pc
                                WHERE pc.deleted_at IS NULL;";

			$result = $this->mysqli->query($query);

			if ($result) {
				$project_categories = [];
				while ($row = $result->fetch_assoc()) {
					$project_categories[] = $row;
				}
				echo json_encode(["project_categories" => $project_categories]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

        // "GET /v1/project/category/(\w+)"
		public function get_project_category_by_uuid($project_category_uuid) {
			$query = "  SELECT pc.uuid, pc.name as project_category_name
								FROM project_category pc
                                WHERE pc.uuid = ?;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_category_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "project_category not found"]);
				exit();
			}

			$project_category = $result->fetch_assoc();
			echo json_encode(["project_category" => $project_category]);
		}

        // "POST /v1/project/category/"
		public function create_project_category() {
			$token = validate_token();

			$data = validate_payload(["name"]);

			$query = "INSERT INTO project_category ( name)
						VALUES (?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $data->name);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Categoria do projeto cadastrada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar categoria do projeto."]);
			}
		}

        // "PUT /v1/project/category/(\w+)"
		public function update_project_category($project_category_uuid) {

			validate_token();

			$data = validate_payload( ["name"]);

			$query = "UPDATE project_category SET name = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('ss', $data->name, $project_category_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Categoria do projeto atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar categoria do projeto."]);
			}
		}

        // "DELETE /v1/project/category/(\w+)"
		public function delete_project_category($project_uuid) {
			$query = "UPDATE project_category SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Categoria de projeto desativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desativar categoria do projeto."]);
			}
		}

          // "PUT /v1/project/category/reactivate/(\w+)"
		public function reactivate_project_category($project_uuid) {
			$query = "UPDATE project_category SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Categoria de projeto reativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar categoria do projeto."]);
			}
		}

	}