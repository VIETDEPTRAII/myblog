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

     // Route for get all blogs and post new blog
    'blog' => array(array('get', new Route('restapi/all_posts')), array('post', new Route('restapi/new_post'))),

    // Route for get blog detail, delete blog detail by physical, delete blog detail by logic and put blog detail
    'blog/(:any)' => array(array('get', new Route('restapi/post_detail/$1')), 
    array('delete', new Route('restapi/post_detail_by_physical/$1')), 
    array('put', new Route('restapi/post_detail/$1'))), 

    // Route for delete blog detail by logic
    'delete_blog_detail/(:any)' => array(array('put', new Route('restapi/post_detail_by_logic/$1'))),
);
