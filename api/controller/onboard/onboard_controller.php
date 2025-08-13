<?php
    class OnboardController {
        private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

        public function init() {
            echo json_encode(["message" => "API is on!"]);
        }

        public function init_v1() {
            echo json_encode(["message" => "V1 is on!"]);
        }    
    }
?>
