<?php
include 'SessionKeyValidationUseCase.php';

class MyRest extends Controller_Rest
{
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
}
