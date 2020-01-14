<?php

class Loginrequest
{
    private $username;
    private $password;

    public function set_username($username)
    {
        $this->username = $username;
    }

    public function set_password($password)
    {
        $this->password = $password;
    }

    public function get_username()
    {
        return $this->username;
    }

    public function get_password()
    {
        return $this->password;
    }

    public function get_users()
    {
        $user = Model_Accounts::find('all');
        return $user;
    }

}