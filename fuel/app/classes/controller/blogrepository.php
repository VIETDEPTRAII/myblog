<?php

class Controller_BlogRepository
{
    public function get_all_posts($offset, $limit)
    {
        $total_of_posts = Model_Posts::query()->count();
        $posts = Model_Posts::query()->where('deleted_date', null)
            ->order_by('created_date', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        $data = array($total_of_posts, $posts);
        return $data;
    }
}
