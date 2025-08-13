<?php
    class CountryController {
        private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

        public function get_countries() {
            $query = "SELECT * FROM country";
            $result = $this->mysqli->query($query);

            if (!$result) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
                exit();
            }

            $countries = [];
            while ($row = $result->fetch_assoc()) {
                $countries[] = $row;
            }

            echo json_encode(["countries" => $countries]);
        }        
    }
?>
