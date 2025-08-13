<?php
    class PaymentMethodController {
        private $mysqli;

        public function __construct($mysqli) {
            $this->mysqli = $mysqli;
        }

        public function get_payment_methods() {
            $query = "SELECT * FROM payment_method";
            $result = $this->mysqli->query($query);

            if (!$result) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $this->mysqli->error]);
                exit();
            }

            $payment_methods = [];
            while ($row = $result->fetch_assoc()) {
                $payment_methods[] = $row;
            }

            echo json_encode(["payment_methods" => $payment_methods]);
        }        
    }
?>
