<?php
include 'LoginUsecase.php';
include 'MyRest.php';

class Controller_Auth_LoginInAPI extends MyRest
{
    protected $format = 'json';

    public function post_registration()
    {
        $username = \Input::json('username');
        $email = \Input::json('email');
        $password = \Input::json('password');
        $group_id = \Input::json('group_id');
        $fullname = \Input::json('fullname');
        $age = \Input::json('age');

        if ($username !== null AND $email !== null AND $password !== null)
        {
            $user = Auth::create_user(
                $username,
                $password,
                $email,
                $group_id,
                array(
                    'fullname' => $fullname,
                    'age' => $age
                )
            );
            return $this->response(array(
                'message' => 'Created user successfully!'
            ), 201);
        }
        else
        {
            return $this->response(array(
                'message' => 'Can not create user!',
                'error' => 'Bad request'
            ), 400);
        }
    }

    public function post_login()
    {
        $login_usecase = new LoginUsecase();
        $session_key = $login_usecase->login(\Input::json('username'), \Input::json('password'));
        return $this->response(array(
            'session-key' => $session_key
        ));
    }
}
