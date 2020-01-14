<?php
include 'sessiontokenprovider.php';

class Loginusecase 
{
    public function login($username, $password)
    {
        $user = Model_Accounts::find('first', array(
            'where' => array(
                'email' => $username,
            ),
        ));

        if ($user !== null and password_verify($password, $user->password))
        {
            $session_key = new SessionTokenProvider();
            return $session_key->generate_session_key();
        }
        else
        {
            return 'Invalid username/password supplied.';
        }
    }
}