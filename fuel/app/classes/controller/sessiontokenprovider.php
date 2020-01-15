<?php
include 'randomuuid.php';
include 'randomtoken.php';
include 'sessiontokenrepository.php';

use \Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class SessionTokenProvider
{

    public function generate_session_key($user_id, $user_email, $user_role)
    {
        $session_key = new RandomUUID();
        $key = "secret";
        $now = date('Y-m-d H:i:s', time());
        $iat = strtotime($now);
        $exp = $iat +  1579663275;
        $payload = array(
            "iss" => "vietdeptrai",
            "iat" => $iat,
            "exp" => $exp,
            "data" => array(
                "id" => $user_id,
                "email" => $user_email,
                "role" => $user_role
            )
        );
        $token = JWT::encode($payload, $key);
        $session_token = new SessionTokenRepository();
        $session_token->save_session_token($session_key->gen_uuid(), $token);
        return $token;
    }
}
