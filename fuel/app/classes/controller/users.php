<?php

class Controller_Users extends Controller_Rest
{

    protected $format = 'json';

    public function get_all_users()
    {
        $users = Model_Accounts::query()->select(array('password' => false))->get();

        return $this->response(array(
            'data' => $users
        ));
    }

    public function get_user_detail($id)
    {
        $users = Model_Accounts::query()
        ->select(array('password' => false))
        ->where('id', '=', $id)
        ->get();

        if ($users != null)
        {
            return $this->response(array(
                'data' => $users
            ));
        }
        return Response::forge('The user with id '.$id.' does not exist!', 404);
    }

    public function post_new_user()
    {
        $user = new Model_Accounts();
        $password = \Input::json('password');
        $password_hash = password_hash($password, PASSWORD_BCRYPT, array(
            'cost' => 12
        ));

        $user->email = \Input::json('email');
        $user->password = $password_hash;
        $user->created_date = date('Y-m-d H:i:s');
        $user->role_id = 3;
        $user->save();

        return $this->response(array(
            'data' => $user
        ));
    }

    public function post_login()
    {
        $password = \Input::json('password');
        $token = bin2hex(openssl_random_pseudo_bytes(64));

        $user = Model_Accounts::find('first', array(
            'where' => array(
                array('email' => \Input::json('email')),
            ),
        ));

        if ( $user != null and password_verify($password, $user->password))
        {
            return $this->response(array(
                'data' => array(
                    'email' => $user->email,
                    'token' => $token,
                ),
            ));
        }
        return Response::forge('Login failed!', 404);
    }

}
