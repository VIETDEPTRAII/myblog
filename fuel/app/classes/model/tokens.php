<?php

class Model_Tokens extends Orm\Model
{
    protected static $_properties = array(
        'id',
        'session_key',
        'token'
    );
}