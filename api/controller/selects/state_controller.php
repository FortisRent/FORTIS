<?php
    class StateController {
        private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

        public function get_state() {
            $query = "SELECT * FROM geo_state";
            $result = $this->mysqli->query($query);

            if (!$result) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
                exit();
            }

            $states = [];
            while ($row = $result->fetch_assoc()) {
                $states[] = $row;
            }

            echo json_encode(["states" => $states]);
        }        
    }
?>