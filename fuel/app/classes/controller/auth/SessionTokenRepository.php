<?php
class SessionTokenRepository
{
    public function save_session_token($session_key, $token, $user_id)
    {
        // find token by user_id
        $user = Model_Tokens::find('first', array(
            'where' => array(
                'user_id' => $user_id,
            ),
        ));

        // create new session_token
        if ($user === null)
        {
            $session_token = new Model_Tokens();
            $session_token->session_key = $session_key;
            $session_token->token = $token;
            $session_token->issued_date = date('Y-m-d H:i:s');
            $session_token->expiration_date = date('Y-m-d H:i:s', strtotime('+1 week'));
            $session_token->user_id = $user_id;
            $session_token->save();
        }

        // update session_token
        else
        {
            $user->set(array(
                'session_key' => $session_key,
                'token' => $token,
                'issued_date' => date('Y-m-d H:i:s'),
                'expiration_date' => date('Y-m-d H:i:s', strtotime('+1 week'))
            ));
            $user->save();
        }
    }
}
