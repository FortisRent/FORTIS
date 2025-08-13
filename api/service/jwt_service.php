<?php 
    function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

	function generate_token($user) {
        $header = base64UrlEncode('{"alg": "HS256", "typ": "JWT"}');
    
        $payloadData = [
            "sub"       => md5(time()),
            "uuid"      => $user['uuid'],
            "phone"     => $user['phone'],
            "full_name" => $user['full_name'],
            "iat"       => time() + 7*24*60*60,
        ];
    
        $payload = base64UrlEncode(json_encode($payloadData));
    
        $signature = base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, 'segredo', true));
    
        $jwt = $header . '.' . $payload . '.' . $signature;
    
        return $jwt;
    }

    function validate_token () {
        if (!isset($_SERVER['HTTP_TOKEN'])) {
            http_response_code(401);
            echo json_encode(["message" => "Ops, você não está logado. Faça o login para continuar."]);
            die();
        }

        $token = $_SERVER['HTTP_TOKEN'];

        $parts = explode('.', $token);

        $parts[2] = rtrim($parts[2], '%');
    
        $signature = base64UrlEncode(
            hash_hmac('sha256', $parts[0] . '.' . $parts[1], 'segredo', true)
        );
    
        if($signature == $parts[2])
        {
            $payload = json_decode(
                base64_decode($parts[1])
            );
    
            return $payload;
        }
        else
        {
            http_response_code(401);
            echo json_encode(["message" => "Token Inválido."]);
            die();
        }
    }
