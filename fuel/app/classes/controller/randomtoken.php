<?php

use \Firebase\JWT\JWT;

class RandomToken
{
    public function gen_token()
    {
        $key = "secret";
        $now = date('Y-m-d H:i:s', time());
        $iat = strtotime($now);
        $exp = $iat +  1579663275;
        $payload = array(
            "iss" => "vietdeptrai",
            "iat" => $iat,
            "exp" => $exp,
            "data" => array(
                "id" => 1,
                "email" => 'vietdeptrai@gmail.com',
                "role" => 3
            )
        );
        $token = JWT::encode($payload, $key);
        return $token;
    }
}