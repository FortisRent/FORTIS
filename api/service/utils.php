<?php
    function base64ErlEncode(string $data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    function random_str(int $length = 6, string $keyspace = '0123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz'): string
    {

        if ($length < 1)
        {
            throw new \RangeException("Length must be a positive integer");
        }

        $pieces = [];

        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i)
        {
            $pieces []= $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
	}

    function generate_uuid_v3(int $bytes = 32, string $keyspace = '0123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz'): string
    {
        if ($bytes < 1)
        {
            throw new \RangeException("Length must be a positive integer");
        }

        $pieces = [];

        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $bytes; ++$i)
        {
            $pieces []= $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
	}

    function validate_payload($required_fields) {

		$payload = json_decode(file_get_contents('php://input'));

        if (!$payload) {
            http_response_code(400);
            echo json_encode(["error" => "JSON payload is missing or invalid."]);
            exit();
        }

        $missing_fields = [];

        foreach ($required_fields as $field) {
            if (!property_exists($payload, $field) || empty($payload->$field)) {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            http_response_code(400);
            echo json_encode(["error" => "The following fields are required: " . implode(', ', $missing_fields)]);
            exit();
        }

        return $payload;
    }

    function generate_identifier(int $bytes = 16, string $chars = '123456789ABCDEFGHJKLMNOPQRSTUVWXYZ'): string
    {
        $uuidBytes = bin2hex($bytes);
        $uuidHex = str_replace(['-', '-'], '', $uuidBytes);
        
        $result = '';
        // $chars = '123456789ABCDEFGHJKLMNOPQRSTUVWXYZ';
        
         srand((float) microtime() * 1000000);
        
        $randomNumber = (int) random_int(0, 0xffffffff); // 32-bit integer
        
        // Generate the identifier based on original logic
        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $index = ($randomNumber % 28) + 1;
                
                if (isset($chars[$index - 1])) {
                    $result .= $chars[$index - 1];
                }
                $randomNumber = (int) ($randomNumber / 28);
            }
        }

        return $result;
	}

    function cleanJsonData($jsonString) {
        $data = !empty($jsonString) ? json_decode($jsonString, true) : null;
        if (is_array($data)) {
            return array_filter($data, function ($value) {
                return !is_null($value) && $value !== '';
            });
        }
        return null;
    }