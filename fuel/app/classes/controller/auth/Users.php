<?php
include 'LoginUsecase.php';
include 'SessionKeyValidationUseCase.php';

class Controller_Auth_Users extends Controller_Rest
{
    protected $format = 'json';

    public function get_all_users()
    {
        $users = Model_Accounts::query()->select(array('password' => false))->get();

        return $this->response($users, 200);
    }

    public function get_user_detail($id)
    {
        $users = Model_Accounts::query()
        ->select(array('password' => false))
        ->where('id', '=', $id)
        ->get();

        if ($users != null)
        {
            return $this->response($users, 200);
        }
        return $this->response('The user with id '.$id.' does not exist!', 404);
    }

    public function post_new_user()
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

    public function post_validate_session_key()
    {
        $auth_header = \Input::headers('Authorization');
        if ($auth_header)
        {
            $sessionKey_validation = new SessionKeyValidationUseCase();
            $result = $sessionKey_validation->find_token_by_session_key($auth_header);
            return Response::forge($result);
        }
        else
        {
            return Response::forge('401 unauthorized', 401);
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
