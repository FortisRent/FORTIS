<?php
	require_once('./service/utils.php');

	// {
	// 	"name": "Escavadeira",
	// 	"description": ""
	// }

	class MachineCategoryController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

        // "GET /v1/machine/category/"
		public function get_all_machine_categories() {
			$query = " SELECT mc.uuid, mc.name as category_name, mc.description as category_description
								FROM machine_category mc
                                WHERE mc.deleted_at IS NULL;";

			$result = $this->mysqli->query($query);

			if ($result) {
				$machine_categories = [];
				while ($row = $result->fetch_assoc()) {
					$machine_categories[] = $row;
				}
				echo json_encode(["machine_categories" => $machine_categories]);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
			}
		}

		public function get_machine_category_by_group_uuid($category_group_uuid) {
			$query = "  SELECT mc.id, mc.uuid, mc.name as category_name
								FROM machine_category mc
								INNER JOIN machine_category_group_rel mcgr ON mcgr.machine_category_id = mc.id
                                INNER JOIN machine_category_group mcg ON mcgr.machine_category_group_id = mcg.id
                                WHERE mcg.uuid = ? AND mc.deleted_at IS NULL;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $category_group_uuid);
			$stmt->execute();
			$result = $stmt->get_result();
        
            if ($result) {
                $machine_category = [];
				while ($row = $result->fetch_assoc()) {
					$machine_category[] = $row;
				}

                http_response_code(200);
                echo json_encode(["machine_category" => $machine_category]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => "Erro ao buscar as categorias do equipamento: " . $this->mysqli->error]);
            }
		}

		public function get_machine_category_by_project_category_uuid($project_category_uuid) {
			$query = "  SELECT mc.id, mc.uuid, mc.name as category_name
								FROM machine_category mc
								INNER JOIN machine_category_group_rel mcgr ON mcgr.machine_category_id = mc.id
                                INNER JOIN machine_category_group mcg ON mcgr.machine_category_group_id = mcg.id
                                INNER JOIN project_category pc ON mcg.project_category_id = pc.id
                                WHERE pc.uuid = ?;";

			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $project_category_uuid);
			$stmt->execute();
			$result = $stmt->get_result();
        
            if ($result) {
                $machine_category = [];
				while ($row = $result->fetch_assoc()) {
					$machine_category[] = $row;
				}

                http_response_code(200);
                echo json_encode(["machine_category" => $machine_category]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => "Erro ao buscar as categorias do equipamento: " . $this->mysqli->error]);
            }
		}

        // "GET /v1/machine/category/(\w+)"
		public function get_machine_category_by_uuid($machine_category_uuid) {
			$query = "  SELECT mc.uuid, mc.name as category_name, mc.description as category_description
								FROM machine_category mc
                                WHERE mc.uuid = ?;";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_category_uuid);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				echo json_encode(["error" => "machine_category not found"]);
				exit();
			}

			$machine_category = $result->fetch_assoc();
			echo json_encode(["machine_category" => $machine_category]);
		}

        // "POST /v1/machine/category/"
		public function create_machine_category() {
			$token = validate_token();

			$data = validate_payload(["name"]);

			$uuid = generate_uuid_v3(64);

			$query = "INSERT INTO machine_category (uuid, name, description)
						VALUES (?, ?, ?)";
						
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('sss', $uuid, $data->name, $data->description);

			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode(["message" => "Categoria do equipamento cadastrada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao cadastrar categoria do equipamento."]);
			}
		}

        // "PUT /v1/machine/category/(\w+)"
		public function update_machine_category($machine_category_uuid) {

			validate_token();

			$data = validate_payload( ["name", "description"]);

			$query = "UPDATE machine_category SET name = ?, description = ? WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);

			$stmt->bind_param('sss', $data->name, $data->description, $machine_category_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Categoria do equipamento atualizada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao atualizar categoria do equipamento."]);
			}
		}

        // "DELETE /v1/machine/category/(\w+)"
		public function delete_machine_category($machine_uuid) {
			$query = "UPDATE machine_category SET deleted_at = CURRENT_TIMESTAMP() WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Máquina desativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao desativar máquina."]);
			}
		}

          // "PUT /v1/machine/category/reactivate/(\w+)"
		public function reactivate_machine_category($machine_uuid) {
			$query = "UPDATE machine_category SET deleted_at = NULL WHERE uuid = ?";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param('s', $machine_uuid);

			if ($stmt->execute()) {
				http_response_code(200);
				echo json_encode(["message" => "Máquina reativada."]);
			} else {
				http_response_code(500);
				echo json_encode(["message" => "Erro ao reativar máquina."]);
			}
		}

	}