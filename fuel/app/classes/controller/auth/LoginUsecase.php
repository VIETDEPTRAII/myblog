<?php
include 'SessionTokenProvider.php';

class LoginUsecase
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
            return $session_key->generate_session_key($user->id, $user->email, $user->role_id);
        }
        else
        {
            return 'Invalid username/password supplied.';
        }
    }
}
