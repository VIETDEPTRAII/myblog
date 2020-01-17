<?php
include 'LoginUsecase.php';
include 'MyRest.php';

class Controller_Auth_LoginInAPI extends MyRest
{
    protected $format = 'json';

    public function post_registration()
    {
        $email = \Input::json('email');
        $password = \Input::json('password');
        $password_hash = password_hash($password, PASSWORD_BCRYPT, array(
            'cost' => 12
        ));

        $user = new Model_Accounts();
        $user->email = $email;
        $user->password = $password_hash;
        $user->created_date = date('Y-m-d H:i:s');
        $user->role_id = 3;

        if ($user->password != null and !empty($user->password)
            and $user->email != null and !empty($user->email))
        {
            $user->save();
            return $this->response('User was created!', 201);
        }
        return $this->response('Unable to create user!', 400);
    }

    public function post_login()
    {
        $login_usecase = new LoginUsecase();
        $session_key = $login_usecase->login(\Input::json('email'), \Input::json('password'));
        return $this->response(array(
            'session-key' => $session_key,
        ));
    }
}
