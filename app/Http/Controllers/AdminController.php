<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Role;
use App\Permission;
use App\Menu;
use App\Submenu;

use Hash;
use Log;
use Validator;
use Auth;
use HasPermission;

class AdminController extends Controller
{
	protected $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

	public function createUser(Request $request)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$validator = Validator::make($request->all(), [
                'name' => 'required',
                'username' => 'required|unique:users',
                'password' => 'required|confirmed'
            ]);

            if ($validator->fails()) {
                if ($validator->messages()->get('username') == ['The username has already been taken.']) {
                    return response()->json(['result' => 'USEREXIST']);
                }
                elseif ($validator->messages()->get('password') == ['The password confirmation does not match.']) {
                	return response()->json(['result' => 'PASSWORDNOTMATCH']);
                }

                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

			$user = User::create([
				'name' => $request->name,
				'username' => $request->username,
				'email' => $request->email,
				'password' => Hash::make($request->password),
				'status' => 'A'
			]);

			$user->roles()->sync($request->role_id);

			return response()->json(['result' => 'GOOD', 'data' => $user]);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function listUser()
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$users = User::where('id', '!=', 1)->orderBy('created_at', 'desc')->get()->map(function ($item) {
				if ($item->status == 'A') {
					$status = 'Active';
				}
				elseif ($item->status == 'S') {
					$status = 'Suspended';
				}

				$item->setAttribute('status', $status);
				$item->setAttribute('roles_name', $item->roles()->pluck('name'));
				$item->setAttribute('roles_id', $item->roles()->pluck('role_id'));

				return $item;
			});

			return response()->json(['result' => 'GOOD', 'data' => $users]);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function showUser(Request $request)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$user = User::find($request->user_id);

			return response()->json(['result' => 'GOOD', 'data' => $user]);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function deleteUser($user_id)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			if ($user_id == 1) {
				return response()->json(['result' => 'ERROR', 'msg' => 'Cannot delete admin.']);
			}

			User::find($user_id)->delete();

			return response()->json(['result' => 'GOOD']);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function updateUser(Request $request)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$validator = Validator::make($request->all(), [
                'name' => 'required',
                'username' => 'required',
                'password' => 'nullable|confirmed'
            ]);

            if ($validator->fails()) {
                if ($validator->messages()->get('password') == ['The password confirmation does not match.']) {
                	return response()->json(['result' => 'PASSWORDNOTMATCH']);
                }

                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

            if ($request->user_id == 1) {
            	return response()->json(['result' => 'ERROR', 'msg' => 'Cannot edit admin.']);
            }

	    	$user = User::find($request->user_id);
	    	$user->update([
	    		'name' => $request->name,
	    		'username' => $request->username,
	    		'email' => $request->email
	    	]);

	    	if (!is_null($request->password) && strlen($request->password) > 0) {
	    		$user->update(['password' => Hash::make($request->password)]);
	    	}

	    	$user->roles()->sync($request->role_id);

	    	return response()->json(['result' => 'GOOD']);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
	}

	public function createRole(Request $request)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

			$permissions_value = Permission::whereIn('id', $request->permission_id)->sum('value');

			Role::create([
				'name' => $request->name,
				'value' => $permissions_value
			]);

			return response()->json(['result' => 'GOOD']);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function listRole()
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

	    	$roles = Role::get();
	    	$permissions = Permission::get();
	    	$array_permissions_id = array();
	    	$array_permissions_name = array();
	    	$array2 = array();

	    	foreach ($roles as $role) {
    			$array_permissions_id = array();
    			$array_permissions_name = array();

	    		foreach ($permissions as $permission) {
	    			if (($role->value & $permission->value) == $permission->value) {
		    			array_push($array_permissions_id, $permission->id);
		    			array_push($array_permissions_name, $permission->name);
	    			}
	    		}

    			array_push($array2, [
    				'role_id' => $role->id,
    				'role_name' => $role->name,
    				'permissions_id' => $array_permissions_id,
    				'permissions_name' => $array_permissions_name
    			]);
	    	}

	    	return response()->json(['result' => 'GOOD', 'data' => $roles, 'permission_role' => $array2]);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

	public function deleteRole($role_id)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			Role::find($role_id)->delete();

			return response()->json(['result' => 'GOOD']);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);	
		}
	}

	public function updateRole(Request $request)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

	    	$role = Role::find($request->role_id);
	    	$role->update(['name' => $request->name]);

	    	$permissions_sum = Permission::whereIn('id', $request->permission_id)->sum('value');

	    	if ($role->value > $permissions_sum) {
	    		$minus_value = $role->value - $permissions_sum;
	    		$role->update(['value' => $role->value - $minus_value]);
	    	}
	    	elseif ($role->value < $permissions_sum) {
	    		$add_value = $permissions_sum - $role->value;
	    		$role->update(['value' => $role->value + $add_value]);
	    	}

	    	return response()->json(['result' => 'GOOD']);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
	}

	public function createPermission(Request $request)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

			$pluck_role_value = Permission::pluck('value')->toArray();
			$max_role_value = max($pluck_role_value);
			$y = log($max_role_value, 2);

			for ($i=0; $i <= $y; $i++) {
				$power_of = pow(2, $i);
				$permission = Permission::where('value', $power_of)->first();

				if (is_null($permission)) {
					Permission::create([
						'name' => $request->name,
						'value' => $power_of
					]);

					return response()->json(['result' => 'GOOD']);
				}
			}

			$power_of = pow(2, $y + 1);

			Permission::create([
				'name' => $request->name,
				'value' => $power_of
			]);

			return response()->json(['result' => 'GOOD']);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function listPermission()
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$permissions = Permission::get();

			return response()->json(['result' => 'GOOD', 'data' => $permissions]);	
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function deletePermission($permission_id)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			Permission::find($permission_id)->delete();

			return response()->json(['result' => 'GOOD']);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
		}
	}

	public function updatePermission(Request $request)
	{
		try {
			$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

			$validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

			$permission = Permission::find($request->permission_id);
			$permission->update(['name' => $request->name]);

			return response()->json(['result' => 'GOOD']);
		}
		catch (\Exception $e) {
			Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);	
		}
	}

    public function createMenu(Request $request)
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

    		$validator = Validator::make($request->all(), [
                'name' => 'required',
                'slug' => 'required',
                'icon' => 'required',
                'order' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

	    	$menu = Menu::create([
	    		'name' => $request->name,
	    		'slug' => $request->slug,
	    		'icon' => $request->icon,
	    		'order' => $request->order
	    	]);

	    	$menu->roles()->sync($request->role_id);

	    	return response()->json(['result' => 'GOOD', 'data' => $menu]);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function listMenu()
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

	    	$roles = Menu::orderBy('order', 'asc')->get()->map(function ($item) {
	    		$item->setAttribute('roles_name', $item->roles()->pluck('name'));
	    		$item->setAttribute('roles_id', $item->roles()->pluck('role_id'));

	    		return $item;
	    	});

	    	return response()->json(['result' => 'GOOD', 'data' => $roles]);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function deleteMenu($menu_id)
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

	    	Menu::find($menu_id)->delete();

			return response()->json(['result' => 'GOOD']);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function updateMenu(Request $request)
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

    		$validator = Validator::make($request->all(), [
                'name' => 'required',
                'slug' => 'required',
                'icon' => 'required',
                'order' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

	    	$menu = Menu::find($request->menu_id);
	    	$menu->update([
	    		'name' => $request->name,
	    		'slug' => $request->slug,
	    		'icon' => $request->icon,
	    		'order' => $request->order
	    	]);

	    	$menu->roles()->sync($request->role_id);

	    	return response()->json(['result' => 'GOOD']);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function createSubMenu(Request $request)
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

    		$validator = Validator::make($request->all(), [
    			'menu_id' => 'required',
                'name' => 'required',
                'slug' => 'required',
                'icon' => 'required',
                'order' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

	    	$sub_menu = Submenu::create([
	    		'menu_id' => $request->menu_id,
	    		'name' => $request->name,
	    		'slug' => $request->slug,
	    		'icon' => $request->icon,
	    		'order' => $request->order
	    	]);

	    	$sub_menu->roles()->sync($request->role_id);

	    	return response()->json(['result' => 'GOOD', 'data' => $sub_menu]);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function listSubMenu()
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

	    	$roles = Submenu::orderBy('order', 'asc')->get()->map(function ($item) {
	    		$menu = Menu::find($item->menu_id);
	    		$item->setAttribute('menu', $menu->name);
	    		$item->setAttribute('roles_name', $item->roles()->pluck('name'));
	    		$item->setAttribute('roles_id', $item->roles()->pluck('role_id'));

	    		return $item;
	    	});

	    	return response()->json(['result' => 'GOOD', 'data' => $roles]);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function deleteSubMenu($submenu_id)
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }

	    	Submenu::find($submenu_id)->delete();

			return response()->json(['result' => 'GOOD']);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function updateSubMenu(Request $request)
    {
    	try {
    		$check_permission = HasPermission::check(['admin-tasks'], $this->user->id);

            if ($check_permission == false) {
                return response()->json(['result' => 'ERROR', 'msg' => 'ACCESSDENIED']);
            }
            
    		$validator = Validator::make($request->all(), [
                'name' => 'required',
                'slug' => 'required',
                'icon' => 'required',
                'order' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
            }

	    	$submenu = Submenu::find($request->submenu_id);
	    	$submenu->update([
	    		'menu_id' => $request->menu_id,
	    		'name' => $request->name,
	    		'slug' => $request->slug,
	    		'icon' => $request->icon,
	    		'order' => $request->order
	    	]);

	    	$submenu->roles()->sync($request->role_id);

	    	return response()->json(['result' => 'GOOD']);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function showSidebarMenu()
    {
    	try {
	    	$role_id = $this->user->roles()->pluck('role_id')->toArray();
	    	$menus = [];

	    	if (!is_null($role_id)) {
		    	$menus = Menu::orderBy('order', 'asc')->get()
		    				->makeHidden(['id', 'name', 'order', 'slug', 'icon', 'created_at', 'updated_at'])
		    				->map(function ($item_menu) use ($role_id) {
					    		if ($item_menu->submenus()->count() > 0) {
					    			$sub = $item_menu->submenus()->orderBy('order', 'asc')->get()
						    			->makeHidden(['id', 'menu_id', 'order', 'slug', 'icon', 'created_at', 'updated_at'])
						    			->map(function ($item_submenu) use ($role_id, $item_menu) {
						    				$submenu_roles = $item_submenu->roles()->pluck('role_id')->toArray();

						    				if (count(array_intersect($role_id, $submenu_roles)) > 0) {
						    					$item_submenu->setAttribute('name', $item_submenu->name);
						    					$item_submenu->setAttribute('url', $item_submenu->slug);
						    					$item_submenu->setAttribute('submenu_icon', $item_submenu->icon);

						    					return $item_submenu;
						    				}
						    			})
						    			->reject(function ($item_submenu) {
						    				return empty($item_submenu);
						    			})
						    			->values();

					    			if (count($sub) > 0) {
					    				$item_menu->setAttribute('parent_menu', $item_menu->name);
						    			$item_menu->setAttribute('sub_menus', $sub);
						    			$item_menu->setAttribute('menu_icon', $item_menu->icon);
					    			}
					    		}
					    		else {
					    			$menu_roles = $item_menu->roles()->pluck('role_id')->toArray();

				    				if (count(array_intersect($role_id, $menu_roles)) > 0) {
				    					$item_menu->setAttribute('parent_menu', $item_menu->name);
				    					$item_menu->setAttribute('url', $item_menu->slug);
				    					$item_menu->setAttribute('menu_icon', $item_menu->icon);
				    				}
					    		}

					    		return $item_menu->toArray();
					    	})
					    	->reject(function ($item_menu) {
					    		return empty($item_menu);
					    	})
					    	->values();
	    	}

	    	return response()->json(['result' => 'GOOD', 'data' => $menus]);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }
}