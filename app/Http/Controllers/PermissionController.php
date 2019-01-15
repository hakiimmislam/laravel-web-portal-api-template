<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use HasPermission;

class PermissionController extends Controller
{
	protected $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function checkAccess(Request $request)
    {
    	$allowed_permissions = HasPermission::check($request->permissions, $this->user->id);

        return response()->json(['allowed' => $allowed_permissions]);
    }
}
