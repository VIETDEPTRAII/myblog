<?php
class SessionKeyValidationUseCase
{
    public function find_token_by_session_key($session_key)
    {
        // Find token by session_key
        $token = Model_Tokens::find('first', array(
            'where' => array(
                'session_key' => $session_key,
            ),
        ));

        // if token is exists -> check the expiration_date of the token
        if ($token !== null)
        {
            $expiration_date = $token->expiration_date;
            $today = Date('Y-m-d H:i:s');
            if (strtotime($expiration_date) <= strtotime($today))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        // if token isn't exist
        else
        {
            return false;
        }
    }
}
