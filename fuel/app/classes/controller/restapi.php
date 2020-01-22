<?php
include __DIR__.'./auth/MyRest.php';
include 'blogrepository.php';

class Controller_RestAPI extends MyRest
{
    protected $format = 'json';
    protected $auth = 'token';

    /**
     * @return object
     */
    public function post_all_posts()
    {
        // Check permission
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.read');

        // If permission return false -> user cannot get all posts
        if ($check_auth === false)
        {
            return $this->response($this->error_403, 403);
        }
        // If has permission -> user can get all posts
        $offset = Input::post('offset', 0);
        $limit = Input::post('limit', 20);

        if ($offset < 0 OR $limit < 0)
        {
            return $this->response(array(
                'message' => 'Offset and Limit must be posotive!',
                'error' => 'Bad request'
            ), 400);
        }
        $blog_repository = new Controller_BlogRepository();
        $data = $blog_repository->get_all_posts($offset, $limit);
        return $this->response(array(
            'total_of_posts' => $data[0],
            'posts' => $data[1]
        ), 200);
    }

    /**
     * @param $id
     * @return object
     */
    public function get_post_detail($id)
    {
        // Check permission
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.read');

        // If permission return true -> user can get post detail
        if ($check_auth === true)
        {
            $post = Model_Posts::find($id, array(
                'where' => array(
                    array('deleted_date', null)
                )
            ));
            if ($post !== null) {
                return $this->response($post, 200);
            }
            return $this->response('The post with id '. $id. ' does not exist!', 404);
        }
        // Else, user cannot get post detail
        else
        {
            return $this->response($this->error_403, 403);
        }
    }

    /**
     * @return object
     * @throws ErrorException
     */
    public function post_new_post()
    {
        // Check permission
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.create');

        // If permission return true -> user can create new post
        if ($check_auth === true)
        {
            $post = new Model_Posts();
            $post->title = \Input::json('title');
            $post->category = \Input::json('category');
            $post->body = \Input::json('body');
            $post->tags = \Input::json('tags');
            $post->created_date = date('Y-m-d H:i:s');
            $post->save();
            return $this->response($post, 201);
        }
        // Else, user cannot create new post
        else
        {
            return $this->response($this->error_403, 403);
        }
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function put_post_detail($id)
    {
        // Check permission
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.update');

        // If permission return true -> user can update post detail
        if ($check_auth === true)
        {
            $post = Model_Posts::find('first', array(
                'where' => array(
                    'id' => $id,
                ),
            ));

            if ($post !== null) 
            {
                $post->set(array(
                    'title' => \Input::json('title'),
                    'category' => \Input::json('category'),
                    'body' => \Input::json('body'),
                    'tags' => \Input::json('tags'),
                    'updated_date' => date('Y-m-d H:i:s'),
                ));
                $post->save();
                return $this->response($post, 200);
            }
            return $this->response('The post with id '. $id. ' does not exist!', 404);
        }
        // Else, user cannot update post detail
        else
        {
            return $this->response($this->error_403, 403);
        }
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function delete_post_detail_by_physical($id)
    {
        // Check permission
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.delete');

        // If permission return true -> user can delete post detail by physical
        if ($check_auth === true)
        {
            $post = Model_Posts::find('first', array(
                'where' => array(
                    array('id' => $id),
                    array('deleted_date', null),
                ),
            ));
    
            if ($post !== null) 
            {
                $post->delete();
                return $this->response('You deleted the post with id '. $id. ' successfully!', 200);
            }
            return $this->response('The post with id '. $id. ' does not exist or deleted by logic!', 404);
        }
        // Else, user cannot delete post detail by physical
        else
        {
            return $this->response($this->error_403, 403);
        }
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function put_post_detail_by_logic($id)
    {
        // Check permission
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.delete');

        // If permission return true -> user can delete post detail by logic
        if ($check_auth === true)
        {
            $post = Model_Posts::find('first', array(
                'where' => array(
                    array('id' => $id),
                    array('updated_date' => \Input::json('updated_date')),
                ),
            ));
    
            if ($post !== null) 
            {
                $post->set(array(
                    'deleted_date' => date('Y-m-d H:i:s')
                ));
                $post->save();
                return $this->response($post, 200);
            }
            return $this->response('The post with id '. $id. ' does not exist!', 404);
        }
        // Else, user cannot delete post detail by logic
        else
        {
            return $this->response($this->error_403, 403);
        }
    }

}
