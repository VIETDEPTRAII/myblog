<?php
class SessionTokenRepository
{
    public function save_session_token($session_key, $token)
    {
        $session_token = new Model_Tokens();
        $session_token->session_key = $session_key;
        $session_token->token = $token;
        $session_token->save();
    }

}
