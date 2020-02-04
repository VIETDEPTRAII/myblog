<?php
include(__DIR__.'/../repository/sessiontokenrepository.php');

use \Firebase\JWT\JWT;

class Helper_SessionTokenProvider
{

    public function generate_uuid()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
    
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
    
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public function generate_session_key($user_id, $user_email, $user_group)
    {
        // generate uuid
        $session_key = $this->generate_uuid();

        // generate token
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
        $session_token_repository = new Repository_SessionTokenRepository();
        $session_token_repository->save_session_token($session_key, $token, $user_id);
        return $session_key;
    }

}
