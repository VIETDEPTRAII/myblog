<?php
include 'RandomUuid.php';
include 'SessionTokenRepository.php';

use \Firebase\JWT\JWT;

class SessionTokenProvider
{

    public function generate_session_key($user_id, $user_email, $user_group)
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
                "group" => $user_group
            )
        );
        $token = JWT::encode($payload, $key_secret);
        if($session_key === null or $token === null) Log::error('$session_key = null or token = null');

        // save session_key and token into database
        $session_token_repository = new SessionTokenRepository();
        $session_token_repository->save_session_token($session_key, $token, $user_id);
        return $session_key;
    }
}
