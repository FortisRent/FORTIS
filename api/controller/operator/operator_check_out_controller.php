<?php
	require_once('./service/utils.php');

	// {
	// 	"appointment_uuid": "72sTGrp8koAf0MEqcFx9LYnSyTvIfaqr",
	// 	"details": "A instrução é que a paciente não beba café com ritalina e depois vá tentar dormir."
	// }

	class OperatorCheckOutController {
		private $mysqli;
		 
		public function __construct($mysqli) {
			$this->mysqli = $mysqli;
		}

		public function create_operator_check_out() {

			validate_token();
			$data = validate_payload(["budget_machine_operator_uuid", "description"]);

			try {
				$this->mysqli->begin_transaction();
		
				$query2 = "INSERT INTO operator_check_out (machine_operator_id, description)
						  VALUES ((SELECT id FROM budget_machine_operator WHERE uuid = ?), ?)";
				
				$stmt2 = $this->mysqli->prepare($query2);
				$stmt2->bind_param('ss', $data->budget_machine_operator_uuid, $data->description);
				
				if (!$stmt2->execute()) {
					throw new Exception("Erro ao adicionar checkout do funcionário.");
				}
				
				$this->mysqli->commit();
				http_response_code(200);
				echo json_encode(["message" => "Checkout feito com sucesso."]);

			} catch (Exception $e) {
				$this->mysqli->rollback();
				http_response_code(500);
				echo json_encode(["message" => $e->getMessage()]);
			}
		}
		
	}