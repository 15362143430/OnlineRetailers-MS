<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

// config里打开route和domain_deploy
//api.vue.com:83 ===> www.tp5.com:83/index.php/api
Route::domain('api', 'api');

/***********  user  *************** */
Route::post("user/look", "user/look");
Route::post('user/login', 'user/login');
Route::post("users", "user/get_user_list");
Route::post("user/add", "user/add_user");
Route::post("user/detail", "user/user_detail");
Route::post("user/edit", "user/user_edit");
Route::post("user/delete", "user/user_delete");

/***********  role  *************** */
Route::post("role/change", "role/role_change");
Route::post("rights", "role/get_right_list");
Route::post("roles", "role/get_role_list");
Route::post("role/right_delete", "role/right_delete");
Route::post("role/right_set", "role/right_set");
Route::post("role/role_edit", "role/role_edit");
Route::post("role/role_delete", "role/role_delete");
Route::post("role/detail", "role/role_detail");
Route::post("role/role_add", "role/role_add");
Route::get("roles","role/role_all");

/***********  menus  *************** */
Route::post("menus", "menu/get_menus");
Route::rule("menu/ll", "menu/ll","POST|GET|DELETE");
/***********  goods  *************** */
Route::get("categories", "goods/get_categories_list");
Route::post("categories", "goods/categories_add");
Route::get("categories_detail", "goods/categories_detail");
Route::put("categories","goods/categories_edit");
Route::put("categories","goods/categories_delete");






// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];
