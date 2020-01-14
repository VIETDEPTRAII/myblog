<?php
include 'randomuuid.php';

use \Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class SessionTokenProvider
{
    private $secret_key = 'vietdeptrai31031998';
    private $expiration_time = 86400000 * 30;

    public function generate_session_key()
    {
        $session_key = new RandomUUID();
        return $session_key->gen_uuid();
    }
}