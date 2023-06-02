<?php


\think\Facade\Route::group([
    'domain'     => config('admin.route.domain'),
//    'prefix'     => config('admin.route.prefix'),
], function (\think\Route $router) {
    $router->get('/login', 'admin/AuthController/loginPage');
//    $router->post('/login', [Controllers\AuthController::class, 'login']);
//    $router->get('/logout', [Controllers\AuthController::class, 'logout']);
//    $router->get('/current-user', [Controllers\AuthController::class, 'currentUser']);
//    $router->get('/captcha', [Controllers\AuthController::class, 'reloadCaptcha']);
//
//    $router->get('/no-content', [Controllers\IndexController::class, 'noContentResponse']);
//    $router->get('/menus', [Controllers\IndexController::class, 'menus']);
//    $router->get('/_settings', [Controllers\IndexController::class, 'settings']);
//    $router->post('/_settings', [Controllers\IndexController::class, 'saveSettings']);
//    $router->get('_download_export', [Controllers\IndexController::class, 'downloadExport']);
//
//    // 用户设置
//    $router->get('/user_setting', [Controllers\AuthController::class, 'userSetting']);
//    $router->put('/user_setting', [Controllers\AuthController::class, 'saveUserSetting']);
//
//    // 图片上传
//    $router->any('upload_image', [Controllers\IndexController::class, 'uploadImage']);
//    // 文件上传
//    $router->any('upload_file', [Controllers\IndexController::class, 'uploadFile']);
//    // 富文本编辑器上传
//    $router->any('upload_rich', [Controllers\IndexController::class, 'uploadRich']);
//
//    // 主页
//    $router->resource('dashboard', Controllers\HomeController::class);
//
//    $router->group(['prefix' => 'system'], function (\Illuminate\Routing\Router $router) {
//        $router->get('/', [Controllers\AdminUserController::class, 'index']);
//        // 管理员
//        $router->resource('admin_users', Controllers\AdminUserController::class);
//        // 菜单
//        $router->resource('admin_menus', Controllers\AdminMenuController::class);
//        // 快速编辑
//        $router->post('admin_menu_quick_save', [Controllers\AdminMenuController::class, 'quickEdit']);
//        // 角色保存权限
//        $router->post('admin_role_save_permissions', [Controllers\AdminRoleController::class, 'savePermissions']);
//        // 角色
//        $router->resource('admin_roles', Controllers\AdminRoleController::class);
//        // 权限
//        $router->resource('admin_permissions', Controllers\AdminPermissionController::class);
//
//        $router->post('_admin_permissions_auto_generate', [
//            Controllers\AdminPermissionController::class,
//            'autoGenerate',
//        ]);
//    });

});
