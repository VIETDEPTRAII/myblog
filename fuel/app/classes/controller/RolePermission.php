<?php

class Controller_RolePermission extends Controller_Rest
{
    protected $format = 'json';

    public function post_registration()
    {
        $username = \Input::json('username');
        $email = \Input::json('email');
        $password = \Input::json('password');
        $group_id = 1; // group_id default of user is 1
        $fullname = \Input::json('fullname');
        $age = \Input::json('age');

        if ($username !== null AND $email !== null AND $password !== null)
        {
            $user = Auth::create_user(
                $username,
                $password,
                $email,
                $group_id,
                array(
                    'fullname' => $fullname,
                    'age' => $age
                )
            );
            return $this->response(array(
                'message' => 'Created user successfully!'
            ), 201);
        }
        else
        {
            return $this->response(array(
                'message' => 'Can not create user!',
                'error' => 'Bad request'
            ), 400);
        }
    }

    // Check permission function
    public function get_check_permission($id)
    {
        Auth::force_login($id);
        $checked_auth = Auth::has_access('blog.create'); // should return true
        if ($checked_auth === true)
        {
            return $this->response('You can access to this page', 200);
        }
        else
        {
            return $this->response('You can not access to this page', 403);
        }
    }

    // Create new permission function
    public function post_permission()
    {
        $permission = \Model\Auth_Permission::forge(array(
            'area' => \Input::json('area'),
            'permission' => \Input::json('permission'),
            'description' => \Input::json('description')
        ));
        $permission->save();
        return $this->response($permission, 201);
    }

    // Create role and permission function
    public function post_role_permission()
    {
        $role_id = \Input::json('role_id');
        $permission_id = \Input::json('permission_id');

        // get the Role identified by $role_id
        $role = \Model\Auth_Role::find($role_id);

        // get the Permission identified by $permission_id
        $permission = \Model\Auth_Permission::find($permission_id);

        if ($role !== null AND $permission !== null)
        {
            // relate the two
            $role->permissions[] = $permission;
            // and save the relation
            $role->save();
            return $this->response($role, 201);
        }
        else
        {
            return $this->response('Role-Permission was not created!', 400);
        }
    }
}
