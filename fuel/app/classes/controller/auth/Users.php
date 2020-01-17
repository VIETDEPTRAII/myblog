<?php
include 'MyRest.php';

class Controller_Auth_Users extends MyRest
{
    protected $format = 'json';
    protected $auth = 'token';

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
