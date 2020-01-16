<?php
include 'randomuuid.php';
include 'sessiontokenrepository.php';

use \Firebase\JWT\JWT;

class SessionTokenProvider
{

    public function generate_session_key($user_id, $user_email, $user_role)
    {
        // gen uuid
        $randome_uuid = new RandomUUID();
        $session_key = $randome_uuid->gen_uuid();

        // gen token
        $key_secret = "secret";
        $issued_date = time();
        $expiration_date = $issued_date + 604800; // Plus 1 week
        $payload = array(
            "iss" => "vietdeptrai",
            "iat" => $issued_date,
            "exp" => $expiration_date,
            "data" => array(
                "id" => $user_id,
                "email" => $user_email,
                "role" => $user_role
            )
        );
        $token = JWT::encode($payload, $key_secret);
        if($session_key === null or $token === null) Log::error('$session_key = null or token = null');

        // save session_key and token into database
        $session_token_repository = new SessionTokenRepository();
        $session_token_repository->save_session_token($session_key, $token);
        return $session_key;
    }
}