<?php

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

//header('Access-Control-Allow-Origin', '*');
//header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
//header('Access-Control-Expose-Headers', 'Authorization, authenticated');
//header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
//header('Access-Control-Allow-Credentials', 'true');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
// ['middleware' => ['test'],'prefix'=>'admin','namespace'=>'Admin']
Route::group(['prefix'=>'admin', 'namespace'=>'Admin'], function () {
    Route::post('/test', function() {
        echo "Cloud studio PHP 项目测试页面";
    });
    Route::post('/login', 'LoginController@login');
    Route::post('/info', 'LoginController@info');
    Route::post('/editPassword', 'LoginController@editPassword');
    Route::get('/getCheckVerifyCode', 'LoginController@getCheckVerifyCode');

    // 网站名称
    Route::post('/webNameInfo', 'WebNameController@info');
    Route::post('/webNameSave', 'WebNameController@save');
    Route::post('/upload', 'WebNameController@upload');
    Route::post('/download', 'WebNameController@download');

    // 权限
    Route::post('/actionIndex', 'RoleActionController@index');
    Route::post('/actionList', 'RoleActionController@list');
    Route::post('/actionDelete', 'RoleActionController@delete');
    Route::post('/actionParentSave', 'RoleActionController@parentSave');
    Route::post('/actionChildrenSave', 'RoleActionController@childrenSave');
    Route::post('/actionChildrenUpdate', 'RoleActionController@childrenUpdate');

    // 角色
    Route::post('/roleIndex', 'RoleController@index');
    Route::post('/roleDelete', 'RoleController@delete');
    Route::post('/roleSave', 'RoleController@save');
    Route::post('/roleInfo', 'RoleController@info');
    Route::post('/roleUpdate', 'RoleController@update');

    // 管理员
    Route::post('/adminIndex', 'AdminController@index');
    Route::post('/adminDelete', 'AdminController@delete');
    Route::post('/adminSave', 'AdminController@save');
    Route::post('/adminUpdate', 'AdminController@update');

    // 首页
    Route::post('/indexCount', 'IndexController@index');
    Route::post('/list', 'IndexController@list');

    // 数字通道
    Route::post('/routeIndex', 'RouteController@index');
    Route::post('/routeDelete', 'RouteController@delete');
    Route::post('/routeSave', 'RouteController@save');
    Route::post('/routeInfo', 'RouteController@info');
    Route::post('/routeUpdate', 'RouteController@update');

    // 数字电缆
    Route::post('/lineIndex', 'LineController@index');
    Route::post('/lineDelete', 'LineController@delete');
    Route::post('/lineSave', 'LineController@save');
    Route::post('/lineInfo', 'LineController@info');
    Route::post('/lineUpdate', 'LineController@update');

    // 柱状标识器
    Route::post('/barIndex', 'BarMarkerController@index');
    Route::post('/barDelete', 'BarMarkerController@delete');
    Route::post('/barSave', 'BarMarkerController@save');
    Route::post('/barInfo', 'BarMarkerController@info');
    Route::post('/barUpdate', 'BarMarkerController@update');

    // 片状标识器
    Route::post('/volumeIndex', 'VolumeMarkerController@index');
    Route::post('/volumeDelete', 'VolumeMarkerController@delete');
    Route::post('/volumeSave', 'VolumeMarkerController@save');
    Route::post('/volumeInfo', 'VolumeMarkerController@info');
    Route::post('/volumeUpdate', 'VolumeMarkerController@update');
});
