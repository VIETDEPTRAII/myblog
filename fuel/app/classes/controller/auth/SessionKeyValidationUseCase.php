<?php
class SessionKeyValidationUseCase
{
    public function find_token_by_session_key($session_key)
    {
        // Find token by session_key
        $token = Model_Tokens::query()->where('session_key', '=', $session_key)->get_one();

        // If token does not exist in database
        if ($token === null)
        {
            return false;
        }
        $expiration_date = $token->expiration_date;
        $today = Date('Y-m-d H:i:s');

        // If token exists in database -> check the expiration date of the token
        if (strtotime($expiration_date) <= strtotime($today))
        {
            return false;
        }

        if (strtotime($expiration_date) > strtotime($today))
        {
            return true;
        }
    }
}
