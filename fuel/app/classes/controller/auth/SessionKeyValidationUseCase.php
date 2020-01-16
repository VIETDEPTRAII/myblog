<?php
class SessionKeyValidationUseCase
{
    public function find_session_key($session_key)
    {
        $token = Model_Tokens::find('first', array(
            'where' => array(
                'session_key' => $session_key,
            ),
        ));

        $exp_date = $token->expiration_date;
        $today = Date('Y-m-d H:i:s');

        if ($token === null or strtotime($exp_date) <= strtotime($today))
        {
            return 'Your session key has expired! Login again, please!';
        }
        else
        {
            return 'Your session key is valid!';
        }
    }
}
