<?php
include 'SessionKeyValidationUseCase.php';

class MyRest extends Controller_Rest
{

    protected $error_403 = array(
        'status' => 403,
        'error' => '403/Forbidden'
    );

    protected function _prepare_token_auth()
    {
        $auth_header = \Input::headers('Authorization');
        if ($auth_header)
        {
            $sessionKey_validation = new SessionKeyValidationUseCase();
            $result = $sessionKey_validation->find_token_by_session_key($auth_header);
            return $result;
        }
        else
        {
            return false;
        }
    }

    protected function _find_user_by_session_key()
    {
        $auth_header = \Input::headers('Authorization');

        if ($auth_header)
        {
            $user = Model_Tokens::query()->where('session_key', '=', $auth_header)->get_one();
            return $user->user_id;
        }
        else
        {
            return false;
        }
    }
}
