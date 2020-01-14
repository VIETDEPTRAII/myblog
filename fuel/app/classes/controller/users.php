<?php
include 'loginrequest.php';
include 'loginusecase.php';

use \Firebase\JWT\JWT;

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

        if ($user->password != null and !empty($user->password)
            and $user->email != null and !empty($user->email))
        {
            $user->save();
            return $this->response(array(
                'message' => 'User was created.'
            ));
        }
        return $this->response(array(
            'message' => 'Unable to create user.'
        ));
    }

    public function post_login()
    {
        $password = \Input::json('password');

        $user = Model_Accounts::find('first', array(
            'where' => array(
                array('email' => \Input::json('email')),
            ),
        ));

        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "data" => array(
                "id" => $user->id,
                "email" => $user->email
            )
        );
        $token = JWT::encode($payload, $key);

        if ($user != null and password_verify($password, $user->password))
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

    
    public function post_login_v2()
    {
        $login_usecase = new Loginusecase();
        $session_key = $login_usecase->login(\Input::json('username'), \Input::json('password'));
        return $this->response(array(
            'session-key' => $session_key,
        ));
    }








    public function post_validate_token()
    {
        $token = \Input::json('token');
        $key = "example_key";
        if ($token)
        {
            // if decode succeed, show user details
            try {
                $decoded = JWT::decode($token, $key, array('HS256'));
                return $this->response(array(
                    'data' => array(
                        'message' => "Access granted.",
                        'data' => $decoded->data
                    ),
                ));
            }
            // if decode fails, it means token is invalid
            catch (Exception $e) {
                return $this->response(array(
                    'data' => array(
                        'message' => "Access granted.",
                        'error' => $e->getMessage()
                    ),
                ));
            }
        }
        // show error message if token is empty
        else
        {
            return Response::forge('Access denied.', 404);
        }
    }

    public function post_change_password($id)
    {
        $user = Model_Accounts::find('first', array(
            'where' => array(
                'id' => $id,
            ),
        ));

        $key = "example_key";
        $token = \Input::json('token');
        $password = \Input::json('password');
        $new_password = \Input::json('new password');
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT, array(
            'cost' => 12
        ));

        if($token and password_verify($password, $user->password))
        {
            try {
                $decoded = JWT::decode($token, $key, array('HS256'));
                $user->set(array(
                    'password' => $password_hash,
                    'updated_date' => date('Y-m-d H:i:s'),
                ));
                if ($user->save())
                {
                    return $this->response(array(
                        'data' => array(
                            'message' => "You changed password successfully!"
                        ),
                    ));
                }
                else
                {
                    return Response::forge("Unable to update user.");
                }
            }
            catch (Exception $e) {
                return $this->response(array(
                    'data' => array(
                        'message' => "Access granted.",
                        'error' => $e->getMessage()
                    ),
                ));
            }
        }
        // show error message if token is empty
        else
        {
            return Response::forge('Access denied or password is wrong.', 404);
        }
    }

}
