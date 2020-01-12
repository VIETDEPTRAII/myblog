<?php
class Model_Posts extends Orm\Model
{
    protected static $_properties = array(
        'id',
        'title',
        'category',
        'body',
        'tags',
        'created_date',
        'updated_date',
        'deleted_date'
    );

}
