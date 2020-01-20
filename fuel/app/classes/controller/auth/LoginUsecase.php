<?php
include 'SessionTokenProvider.php';

class LoginUsecase
{
    public function login($username, $password)
    {
        if(Auth::login($username, $password))
        {
            $user = \Model\Auth_User::find('first', array(
                'where' => array(
                    'username' => $username,
                ),
            ));
            $session_key = new SessionTokenProvider();
            return $session_key->generate_session_key($user->id, $user->email, $user->group);
        }
        else
        {
            return 'Invalid username/password supplied.';
        }
    }
}
