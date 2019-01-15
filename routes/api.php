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
	Route::post('/admin/user/create', 'AdminController@createUser');
	Route::get('/admin/user/list', 'AdminController@listUser');
	Route::post('/admin/user/show', 'AdminController@showUser');
	Route::delete('/admin/user/delete/{user_id}', 'AdminController@deleteUser');
	Route::post('/admin/user/update', 'AdminController@updateUser');

	Route::post('/admin/role/create', 'AdminController@createRole');
	Route::get('/admin/role/list', 'AdminController@listRole');
	Route::delete('/admin/role/delete/{role_id}', 'AdminController@deleteRole');
	Route::post('admin/role/update', 'AdminController@updateRole');

	Route::post('/admin/permission/create', 'AdminController@createPermission');
	Route::get('/admin/permission/list', 'AdminController@listPermission');
	Route::delete('/admin/permission/delete/{permission_id}', 'AdminController@deletePermission');
	Route::post('/admin/permission/update', 'AdminController@updatePermission');
	
	Route::post('/admin/menu/create', 'AdminController@createMenu');
	Route::get('/admin/menu/list', 'AdminController@listMenu');
	Route::delete('/admin/menu/delete/{menu_id}', 'AdminController@deleteMenu');
	Route::post('/admin/menu/update', 'AdminController@updateMenu');

	Route::post('/admin/submenu/create', 'AdminController@createSubMenu');
	Route::get('/admin/submenu/list', 'AdminController@listSubMenu');
	Route::delete('/admin/submenu/delete/{submenu_id}', 'AdminController@deleteSubMenu');
	Route::post('/admin/submenu/update', 'AdminController@updateSubMenu');

	Route::get('/admin/sidebar/menu/show', 'AdminController@showSidebarMenu');

	Route::get('/admin/user/get', 'AdminController@getUser');
	Route::post('/admin/user/reset/password', 'AdminController@resetPasswordUser');

	Route::post('/permission/check', 'PermissionController@checkAccess');
});