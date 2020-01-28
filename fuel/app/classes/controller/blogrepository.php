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

    public function get_post_detail($id)
    {
        $post = Model_Posts::query()
            ->where(array('id' => $id, 'deleted_date' => null))
            ->get_one();
        return $post;
    }

    public function post_new_post($title, $category, $body, $tags)
    {
        $post = new Model_Posts();
        $post->title = $title;
        $post->category = $category;
        $post->body = $body;
        $post->tags = $tags;
        $post->created_date = date('Y-m-d H:i:s');
        $post->save();
        return $post;
    }

}
