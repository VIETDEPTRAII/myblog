<?php
class Model_Accounts extends Orm\Model
{
    protected static $_properties = array(
        'id',
        'email',
        'password',
        'created_date',
        'updated_date',
        'deleted_date',
        'role_id'
    );
}
