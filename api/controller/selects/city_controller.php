<?php
    class CityController {
        private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

        public function get_city() {
            // $query = "SELECT id, name FROM city";
            // $result = $this->mysqli->query($query);

            // if (!$result) {
            //     http_response_code(500);
            //     echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
            //     exit();
            // }

            // $cities = [];
            // while ($row = $result->fetch_assoc()) {
            //     $cities[] = $row;
            // }

            // echo json_encode(["cities" => $cities]);

            echo file_get_contents('./controller/selects/city_list.json');
        }        

        public function get_city_by_geo_state($state_id) {
            $query = "SELECT c.id, c.name
                                    FROM city c
                                    INNER JOIN geo_state gs ON c.state_id = gs.id
                                    WHERE gs.id =  ? ";

            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param('i', $state_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
                exit();
            }

            $city_name_id = [];
            while ($row = $result->fetch_assoc()) {
                $city_name_id[] = $row;
            }

            echo json_encode(["city_name_by_geo_state" => $city_name_id]);
        }
    }
?>