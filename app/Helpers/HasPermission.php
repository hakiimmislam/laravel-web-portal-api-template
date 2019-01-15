<?php

namespace App\Helpers;

use App\User;
use App\Permission;
use App\Role;

class HasPermission
{
	public static function check($action, $user_id)
	{
		$permissions = Permission::whereIn('name', $action)->get();
		$roles_id = User::find($user_id)->roles()->pluck('role_id')->toArray();

		$allowed = array();
		foreach ($roles_id as $role_id) {
		 	$role = Role::find($role_id);

		 	foreach ($permissions as $permission) {
		 		$check = $role->value & $permission->value;

			 	if ($check != 0) {
			 		array_push($allowed, $permission->name);
			 	}
		 	}
		}
		
		return $allowed;
	}
}