<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api']], function () {
	Route::group(['prefix' => 'admin'], function () {
		Route::post('user/create', 'AdminController@createUser');
		Route::get('user/list', 'AdminController@listUser');
		Route::post('user/show', 'AdminController@showUser');
		Route::delete('user/delete/{user_id}', 'AdminController@deleteUser');
		Route::post('user/update', 'AdminController@updateUser');

		Route::post('role/create', 'AdminController@createRole');
		Route::get('role/list', 'AdminController@listRole');
		Route::delete('role/delete/{role_id}', 'AdminController@deleteRole');
		Route::post('role/update', 'AdminController@updateRole');

		Route::post('permission/create', 'AdminController@createPermission');
		Route::get('permission/list', 'AdminController@listPermission');
		Route::delete('permission/delete/{permission_id}', 'AdminController@deletePermission');
		Route::post('permission/update', 'AdminController@updatePermission');
		
		Route::post('menu/create', 'AdminController@createMenu');
		Route::get('menu/list', 'AdminController@listMenu');
		Route::delete('menu/delete/{menu_id}', 'AdminController@deleteMenu');
		Route::post('menu/update', 'AdminController@updateMenu');

		Route::post('submenu/create', 'AdminController@createSubMenu');
		Route::get('submenu/list', 'AdminController@listSubMenu');
		Route::delete('submenu/delete/{submenu_id}', 'AdminController@deleteSubMenu');
		Route::post('submenu/update', 'AdminController@updateSubMenu');

		Route::get('sidebar/menu/show', 'AdminController@showSidebarMenu');
	});

	Route::group(['prefix' => 'account'], function () {
		Route::get('get', 'AccountController@get');
		Route::post('reset/password', 'AccountController@resetPassword');
	});

	Route::post('/permission/check', 'PermissionController@checkAccess');
});