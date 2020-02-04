<?php
include(__DIR__.'/../../repository/postrepository.php');
include(__DIR__.'/../myrest.php');

class Controller_Post_PostApi extends Controller_MyRest
{
    protected $format = 'json';
    protected $auth = 'token';

    /**
     * @return object
     */
    public function get_all_posts()
    {
        // Check user's permission to call API
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
        $post_repository = new Repository_PostRepository();
        $data = $post_repository->get_all_posts($offset, $limit);
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
        // Check user's permission to call API
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.read');

        // If permission return false -> user cannot get post detail
        if ($check_auth === false)
        {
            return $this->response($this->error_403, 403);
        }
        // If has permission -> user can get post detail
        $post_repository = new Repository_PostRepository();
        $data = $post_repository->get_post_detail($id);

        if ($data === null)
        {
            return $this->response(array(
                'message' => 'The post with id '. $id. ' does not exist!',
                'error' => '404 not found'
            ), 404);
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

        // If has not permission -> user cannot create new post
        if ($check_auth === false)
        {
            return $this->response($this->error_403, 403);
        }
        // If has permission -> user can call API to create new post
        // Get user's inputs
        $title = \Input::json('title');
        $category = \Input::json('category');
        $body = \Input::json('body');
        $tags = \Input::json('tags');

        // Start inputs validation
        $post_repository = new Repository_PostRepository();
        $inputs_validation = $post_repository->validate_inputs();

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
        $data = $post_repository->post_new_post($title, $category, $body, $tags);
        return $this->response($data, 201);
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function put_post($id)
    {
        // Check user's permission to call API
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.update');

        // If has not permission -> user cannot call API
        if ($check_auth === false)
        {
            return $this->response($this->error_403, 403);
        }
        // If has permission -> user can call API to update post
        // First, find post by id
        $post_repository = new Repository_PostRepository();
        $post = $post_repository->get_post_detail($id);

        // If post does not exist -> response 404 not found
        if ($post === null)
        {
            return $this->response(array(
                'message' => 'The post with id '. $id. ' does not exist!',
                'error' => '404 not found'
            ), 404);
        }
        // If post exists -> get user's inputs
        $title = \Input::json('title');
        $category = \Input::json('category');
        $body = \Input::json('body');
        $tags = \Input::json('tags');

        // Start inputs validation
        $post_repository = new Repository_PostRepository();
        $inputs_validation = $post_repository->validate_inputs();

        // Inputs validation failed -> response error
        if (!$inputs_validation->run(\Input::json()))
        {
            foreach ($inputs_validation->error() as $field => $error)
            {
                return $this->response(array(
                    'message' => 'Cannot update post detail',
                    'error' => $error->get_message()
                ), 400);
            }
        }
        // Inputs validation successfully -> update the post and response it
        $data = $post_repository->put_post($id, $title, $category, $body, $tags);
        return $this->response($data, 200);
    }

    /**
     * @param $id
     * @return object
     * @throws Exception
     */
    public function delete_post($id)
    {
        // Check user's permission to call API
        $user_id = $this->_find_user_by_session_key();
        Auth::force_login($user_id);
        $check_auth = Auth::has_access('blog.delete');

        // If has not permission -> user cannot call API
        if ($check_auth === false)
        {
            return $this->response($this->error_403, 403);
        }
        // If has permission -> user can call API to delete post
        $post_repository = new Repository_PostRepository();
        $post = $post_repository->delete_post($id);

        // If post does not exist -> response 404 not found
        if ($post === null)
        {
            return $this->response(array(
                'message' => 'The post with id '. $id. ' does not exist or has been deleted!',
                'error' => '404 not found'
            ), 404);
        }
        // If post exist -> delete post and response deleted successfully message
        if ($post === true)
        {
            return $this->response('You deleted the post with id '. $id. ' successfully!', 200);
        }
    }
}
