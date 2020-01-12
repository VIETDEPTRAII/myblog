<?php

class Controller_Posts extends Controller_Template
{
    public function action_index()
    {   
        $posts = Model_Posts::find('all', array(
            'order_by' => array('id' => 'desc'),
        ));

        $data = array('posts' => $posts);
        $this->template->title = 'Blog Posts';
        $this->template->content = View::forge('posts/index', $data, false);
    }

    public function action_add()
    {
        if(Input::post('send')){
            $post = new Model_Posts();
            $post->title = Input::post('title');
            $post->category = Input::post('category');
            $post->body = Input::post('body');
            $post->tags = Input::post('tags');
            $post->created_date = date('Y-m-d H:i:s');
            $post->save();
            // Session::set_flash('Success', 'Post Added');
            Response::redirect('/');
        }

        $data = array();
        $this->template->title = 'Add Post';
        $this->template->content = View::forge('posts/add', $data);
    }

    public function action_view($id)
    {
        $post = Model_Posts::find('first', array(
            'where' => array(
                'id' => $id
            ),
        ));

        $data = array('post' => $post);
        $this->template->title = $post->title;
        $this->template->content = View::forge('posts/view', $data, false);
    }

}    



