<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.8.2
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Default route
	 * -------------------------------------------------------------------------
	 *
	 */

	'_root_' => 'posts/index',

	/**
	 * -------------------------------------------------------------------------
	 *  Page not found
	 * -------------------------------------------------------------------------
	 *
	 */

	'_404_' => 'welcome/404',

    /**
	 * -------------------------------------------------------------------------
	 *  Restful APIs
	 * -------------------------------------------------------------------------
	 *
	 */

     // Route for get all posts and post new post
    'blog' => array(array('get', new Route('post/postapi/all_posts')),
            array('post', new Route('post/postapi/new_post'))),

    // Route for get post detail, delete post and put post
    'blog/(:any)' => array(array('get', new Route('post/postapi/post_detail/$1')),
                    array('delete', new Route('post/postapi/post/$1')),
                    array('put', new Route('post/postapi/post/$1'))),
);
