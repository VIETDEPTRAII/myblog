<?php
include __DIR__.'./auth/MyRest.php';
include 'blogvalidation.php';
include 'blogrepository.php';

class Controller_RestAPI extends MyRest
{
    protected $format = 'json';
    protected $auth = 'token';

    /**
     * @return object
     */
    public function get_all_posts()
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
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 20);

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

        // If permission return false -> user cannot get post detail
        if ($check_auth === false)
        {
            return $this->response($this->error_403, 403);
        }
        // If has permission -> user can get post detail
        $blog_repository = new Controller_BlogRepository();
        $data = $blog_repository->get_post_detail($id);

        if ($data === null)
        {
            return $this->response('The post with id '. $id. ' does not exist!', 404);
        }
        return $this->response($data, 200);
    }

    /**
     * @return object
     * @throws ErrorException
     */
    public function post_new_post()
    {
        // Check user's permission to call API
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.create');

        // If has not permission -> user cannot call API
        if ($check_auth === false)
        {
            return $this->response($this->error_403, 403);
        }
        // If has permission -> user can call API
        $title = \Input::json('title');
        $category = \Input::json('category');
        $body = \Input::json('body');
        $tags = \Input::json('tags');

        // Start inputs validation
        $blog_validation = new Controller_BlogValidation();
        $inputs_validation = $blog_validation->validate_inputs();

        // Inputs validation failed -> response error
        if (!$inputs_validation->run(\Input::json()))
        {
            foreach ($inputs_validation->error() as $field => $error)
            {
                return $this->response(array(
                    'message' => 'Cannot create new post',
                    'error' => $error->get_message()
                ), 400);
            }
        }
        // Inputs validation successfully -> create the post and response it
        $blog_repository = new Controller_BlogRepository();
        $data = $blog_repository->post_new_post($title, $category, $body, $tags);
        return $this->response($data, 201);
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
