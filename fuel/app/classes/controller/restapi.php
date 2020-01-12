<?php

class Controller_RestAPI extends Controller_Rest
{
    protected $format = 'json';

    /**
     * @return object
     */
    public function get_all_posts()
    {
        $posts = Model_Posts::find('all', array(
            'where' => array(
                array('deleted_date', null),
            ),
            'order_by' => array('created_date' => 'desc'),
        ));

        if ($posts !== null)
        {
            return $this->response(array(
                'data' => $posts
            ));
        }
        return Response::forge('Posts do not exist!', 404);
    }

    /**
     * @param $id
     * @return object
     */
    public function get_post_detail($id)
    {
        $post = Model_Posts::find('first', array(
            'where' => array(
                array('id' => $id),
                array('deleted_date', null),
            ),
        ));

        if ($post !== null) {
            return $this->response(array(
                'data' => $post
            ));
        }
        return Response::forge('The post with id '. $id. ' does not exist!', 404);
    }

    /**
     * @return object
     * @throws ErrorException
     */
    public function post_new_post()
    {
        $post = new Model_Posts();
        $post->title = \Input::json('title');
        $post->category = \Input::json('category');
        $post->body = \Input::json('body');
        $post->tags = \Input::json('tags');
        $post->created_date = date('Y-m-d H:i:s');
        $post->save();

        return $this->response(array(
            'data' => $post
        ));
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function put_post_detail($id)
    {
        $post = Model_Posts::find('first', array(
            'where' => array(
                'id' => $id,
            ),
        ));

        if ($post !== null) {
            $post->set(array(
                'title' => \Input::json('title'),
                'category' => \Input::json('category'),
                'body' => \Input::json('body'),
                'tags' => \Input::json('tags'),
                'updated_date' => date('Y-m-d H:i:s'),
            ));
            $post->save();

            return $this->response(array(
                'data' => $post
            ));
        }
        return Response::forge('The post with id '. $id. ' does not exist!', 404);
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function delete_post_detail_by_physical($id)
    {
        $post = Model_Posts::find('first', array(
            'where' => array(
                array('id' => $id),
                array('deleted_date', null),
            ),
        ));

        if ($post !== null) {
            $post->delete();
            return Response::forge('You deleted the post with id '. $id. ' successfully!');
        }
        return Response::forge('The post with id '. $id. ' does not exist or deleted by logic!', 404);
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function put_post_detail_by_logic($id)
    {
        $post = Model_Posts::find('first', array(
            'where' => array(
                array('id' => $id),
                array('updated_date' => \Input::json('updated_date')),
            ),
        ));

        if ($post !== null) {
            $post->set(array(
                'deleted_date' => date('Y-m-d H:i:s')
            ));
            $post->save();
            return $this->response(array(
                'data' => $post
            ));
        }
        return Response::forge('The post with id '. $id. ' does not exist!', 404);
    }

}
