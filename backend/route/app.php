<?php
use think\facade\Route;

Route::post('api/login', [\app\adminapi\controller\auth\LoginController::class, 'account']);

Route::group('api', function () {
    // 用户管理
    Route::get('user/info', [\app\adminapi\controller\admin\UserController::class, 'info']);
    Route::get('user/lists', [\app\adminapi\controller\admin\UserController::class, 'lists']);
    Route::get('user/personal', [\app\adminapi\controller\admin\UserController::class, 'personal']);
    Route::post('user/add', [\app\adminapi\controller\admin\UserController::class, 'add']);
    Route::post('user/edit', [\app\adminapi\controller\admin\UserController::class, 'edit']);
    Route::post('user/delete', [\app\adminapi\controller\admin\UserController::class, 'delete']);
    Route::post('user password/edit', [\app\adminapi\controller\admin\UserController::class, 'editPassword']);
    
    // 角色管理
    Route::get('role/lists', [\app\adminapi\controller\admin\RoleController::class, 'lists']);
    Route::get('role/all', [\app\adminapi\controller\admin\RoleController::class, 'all']);
    Route::get('role/menus', [\app\adminapi\controller\admin\RoleController::class, 'menus']);
    Route::post('role/add', [\app\adminapi\controller\admin\RoleController::class, 'add']);
    Route::post('role/edit', [\app\adminapi\controller\admin\RoleController::class, 'edit']);
    Route::post('role/delete', [\app\adminapi\controller\admin\RoleController::class, 'delete']);
    
    // 菜单管理
    Route::get('menu/lists', [\app\adminapi\controller\admin\MenuController::class, 'lists']);
    Route::get('menu/nav', [\app\adminapi\controller\admin\MenuController::class, 'nav']);
    Route::post('menu/add', [\app\adminapi\controller\admin\MenuController::class, 'add']);
    Route::post('menu/edit', [\app\adminapi\controller\admin\MenuController::class, 'edit']);
    Route::post('menu/delete', [\app\adminapi\controller\admin\MenuController::class, 'delete']);
    
    // 部门管理
    Route::get('dept/lists', [\app\adminapi\controller\admin\DeptController::class, 'lists']);
    Route::get('dept/tree', [\app\adminapi\controller\admin\DeptController::class, 'tree']);
    Route::post('dept/add', [\app\adminapi\controller\admin\DeptController::class, 'add']);
    Route::post('dept/edit', [\app\adminapi\controller\admin\DeptController::class, 'edit']);
    Route::post('dept/delete', [\app\adminapi\controller\admin\DeptController::class, 'delete']);
    
    // 岗位管理
    Route::get('post/lists', [\app\adminapi\controller\admin\PostController::class, 'lists']);
    Route::get('post/all', [\app\adminapi\controller\admin\PostController::class, 'all']);
    Route::post('post/add', [\app\adminapi\controller\admin\PostController::class, 'add']);
    Route::post('post/edit', [\app\adminapi\controller\admin\PostController::class, 'edit']);
    Route::post('post/delete', [\app\adminapi\controller\admin\PostController::class, 'delete']);
    
    // 登录日志
    Route::get('login_log/lists', [\app\adminapi\controller\admin\LoginLogController::class, 'lists']);
    Route::delete('login_log/clean', [\app\adminapi\controller\admin\LoginLogController::class, 'clean']);
    
    // 操作日志
    Route::get('log/lists', [\app\adminapi\controller\admin\LogController::class, 'lists']);
    Route::get('log/detail', [\app\adminapi\controller\admin\LogController::class, 'detail']);
    
    // 字典管理
    Route::get('dict/type/lists', [\app\adminapi\controller\admin\DictTypeController::class, 'lists']);
    Route::get('dict/type/all', [\app\adminapi\controller\admin\DictTypeController::class, 'all']);
    Route::post('dict/type/add', [\app\adminapi\controller\admin\DictTypeController::class, 'add']);
    Route::post('dict/type/edit', [\app\adminapi\controller\admin\DictTypeController::class, 'edit']);
    Route::post('dict/type/delete', [\app\adminapi\controller\admin\DictTypeController::class, 'delete']);
    
    Route::get('dict/data/lists', [\app\adminapi\controller\admin\DictDataController::class, 'lists']);
    Route::get('dict/data/all', [\app\adminapi\controller\admin\DictDataController::class, 'all']);
    Route::post('dict/data/add', [\app\adminapi\controller\admin\DictDataController::class, 'add']);
    Route::post('dict/data/edit', [\app\adminapi\controller\admin\DictDataController::class, 'edit']);
    Route::post('dict/data/delete', [\app\adminapi\controller\admin\DictDataController::class, 'delete']);
    
    // 系统配置
    Route::get('config/lists', [\app\adminapi\controller\admin\SystemConfigController::class, 'lists']);
    Route::post('config/save', [\app\adminapi\controller\admin\SystemConfigController::class, 'save']);
    
    // 文件上传
    Route::post('upload/image', [\app\adminapi\controller\UploadController::class, 'image']);
    Route::post('upload/file', [\app\adminapi\controller\UploadController::class, 'file']);
    Route::get('upload/lists', [\app\adminapi\controller\UploadController::class, 'lists']);
    Route::post('upload/delete', [\app\adminapi\controller\UploadController::class, 'delete']);
    Route::get('upload/config', [\app\adminapi\controller\UploadController::class, 'config']);
    Route::get('upload/statistics', [\app\adminapi\controller\UploadController::class, 'statistics']);
    
    // 通知渠道
    Route::get('notice_channel/lists', [\app\adminapi\controller\NoticeChannelController::class, 'lists']);
    Route::get('notice_channel/detail', [\app\adminapi\controller\NoticeChannelController::class, 'detail']);
    Route::post('notice_channel/add', [\app\adminapi\controller\NoticeChannelController::class, 'add']);
    Route::post('notice_channel/edit', [\app\adminapi\controller\NoticeChannelController::class, 'edit']);
    Route::post('notice_channel/delete', [\app\adminapi\controller\NoticeChannelController::class, 'delete']);
    
    // 消息模板
    Route::get('notice_template/lists', [\app\adminapi\controller\NoticeTemplateController::class, 'lists']);
    Route::get('notice_template/detail', [\app\adminapi\controller\NoticeTemplateController::class, 'detail']);
    Route::post('notice_template/add', [\app\adminapi\controller\NoticeTemplateController::class, 'add']);
    Route::post('notice_template/edit', [\app\adminapi\controller\NoticeTemplateController::class, 'edit']);
    Route::post('notice_template/delete', [\app\adminapi\controller\NoticeTemplateController::class, 'delete']);
    
    // 发送记录
    Route::get('notice_record/lists', [\app\adminapi\controller\NoticeRecordController::class, 'lists']);
    Route::get('notice_record/detail', [\app\adminapi\controller\NoticeRecordController::class, 'detail']);
    Route::get('notice_record/statistics', [\app\adminapi\controller\NoticeRecordController::class, 'statistics']);
    
    // 定时任务
    Route::get('crontab/lists', [\app\adminapi\controller\CrontabController::class, 'lists']);
    Route::get('crontab/detail', [\app\adminapi\controller\CrontabController::class, 'detail']);
    Route::post('crontab/add', [\app\adminapi\controller\CrontabController::class, 'add']);
    Route::post('crontab/edit', [\app\adminapi\controller\CrontabController::class, 'edit']);
    Route::post('crontab/delete', [\app\adminapi\controller\CrontabController::class, 'delete']);
    Route::post('crontab/execute', [\app\adminapi\controller\CrontabController::class, 'execute']);
    Route::post('crontab/toggle_status', [\app\adminapi\controller\CrontabController::class, 'toggleStatus']);
    
    // 支付相关
    Route::get('pay/config/lists', [\app\adminapi\controller\pay\PayConfigController::class, 'lists']);
    Route::get('pay/config/info', [\app\adminapi\controller\pay\PayConfigController::class, 'info']);
    Route::post('pay/config/save', [\app\adminapi\controller\pay\PayConfigController::class, 'save']);
    Route::get('pay/order/lists', [\app\adminapi\controller\pay\PayOrderController::class, 'lists']);
    Route::get('pay/order/detail', [\app\adminapi\controller\pay\PayOrderController::class, 'detail']);
    Route::post('pay/order/close', [\app\adminapi\controller\pay\PayOrderController::class, 'close']);
    Route::post('pay/order/manualPaid', [\app\adminapi\controller\pay\PayOrderController::class, 'manualPaid']);
    Route::get('pay/refund/lists', [\app\adminapi\controller\pay\PayRefundController::class, 'lists']);
    Route::get('pay/refund/detail', [\app\adminapi\controller\pay\PayRefundController::class, 'detail']);
    Route::post('pay/refund/apply', [\app\adminapi\controller\pay\PayRefundController::class, 'apply']);
    
    // 租户管理
    Route::get('tenant/lists', [\app\adminapi\controller\tenant\TenantController::class, 'lists']);
    Route::get('tenant/info', [\app\adminapi\controller\tenant\TenantController::class, 'info']);
    Route::post('tenant/add', [\app\adminapi\controller\tenant\TenantController::class, 'add']);
    Route::post('tenant/edit', [\app\adminapi\controller\tenant\TenantController::class, 'edit']);
    Route::post('tenant/delete', [\app\adminapi\controller\tenant\TenantController::class, 'delete']);
    Route::get('tenant/package_lists', [\app\adminapi\controller\tenant\TenantController::class, 'packageLists']);
    
    // 代码生成器
    Route::get('generator/config_lists', [\app\adminapi\controller\generator\GeneratorController::class, 'configLists']);
    Route::post('generator/config_add', [\app\adminapi\controller\generator\GeneratorController::class, 'configAdd']);
    Route::get('generator/template_lists', [\app\adminapi\controller\generator\GeneratorController::class, 'templateLists']);
    
    // 工作流
    Route::get('workflow/lists', [\app\adminapi\controller\workflow\WorkflowController::class, 'lists']);
    Route::get('workflow/instance_lists', [\app\adminapi\controller\workflow\WorkflowController::class, 'instanceLists']);
    Route::get('workflow/todo_list', [\app\adminapi\controller\workflow\WorkflowController::class, 'todoList']);
    Route::get('workflow/detail', [\app\adminapi\controller\workflow\WorkflowController::class, 'detail']);
    Route::post('workflow/add', [\app\adminapi\controller\workflow\WorkflowController::class, 'add']);
    Route::post('workflow/edit', [\app\adminapi\controller\workflow\WorkflowController::class, 'edit']);
    Route::post('workflow/delete', [\app\adminapi\controller\workflow\WorkflowController::class, 'delete']);
    
    // 表单
    Route::get('form/lists', [\app\adminapi\controller\FormController::class, 'lists']);
    
    // 微信
    Route::get('wechat/account/lists', [\app\adminapi\controller\wechat\WechatAccountController::class, 'lists']);
    Route::get('wechat/account/detail', [\app\adminapi\controller\wechat\WechatAccountController::class, 'detail']);
    Route::post('wechat/account/add', [\app\adminapi\controller\wechat\WechatAccountController::class, 'add']);
    Route::post('wechat/account/edit', [\app\adminapi\controller\wechat\WechatAccountController::class, 'edit']);
    Route::post('wechat/account/delete', [\app\adminapi\controller\wechat\WechatAccountController::class, 'delete']);
    Route::get('wechat/material/lists', [\app\adminapi\controller\wechat\WechatMaterialController::class, 'lists']);
    Route::get('wechat/material/statistics', [\app\adminapi\controller\wechat\WechatMaterialController::class, 'statistics']);
    Route::post('wechat/material/delete', [\app\adminapi\controller\wechat\WechatMaterialController::class, 'delete']);
    Route::get('wechat/menu/lists', [\app\adminapi\controller\wechat\WechatMenuController::class, 'lists']);
    Route::get('wechat/menu/detail', [\app\adminapi\controller\wechat\WechatMenuController::class, 'detail']);
    Route::post('wechat/menu/save', [\app\adminapi\controller\wechat\WechatMenuController::class, 'save']);
    Route::get('wechat/reply/lists', [\app\adminapi\controller\wechat\WechatReplyController::class, 'lists']);
    Route::post('wechat/reply/add', [\app\adminapi\controller\wechat\WechatReplyController::class, 'add']);
    Route::get('wechat/fans/lists', [\app\adminapi\controller\wechat\WechatFansController::class, 'lists']);
    Route::get('wechat/mini_program/lists', [\app\adminapi\controller\wechat\MiniProgramController::class, 'lists']);
    Route::get('wechat/open_platform/lists', [\app\adminapi\controller\wechat\OpenPlatformController::class, 'lists']);
})->middleware(\app\adminapi\http\middleware\InitMiddleware::class)
  ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);
