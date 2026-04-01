<?php
/**
 * 飞羽后台管理系统 - 后台管理 API 路由
 */

use think\facade\Route;

// 登录接口（无需认证）
Route::post('adminapi/login/account', 'adminapi/auth.Login/account');

// 需要认证的接口
Route::group('adminapi', function () {
    // 退出
    Route::post('login/logout', 'adminapi/auth.Login/logout');

    // 用户管理
    Route::get('user/list', 'adminapi/admin.User/list');
    Route::get('user/info', 'adminapi/admin.User/info');
    Route::post('user/add', 'adminapi/admin.User/add');
    Route::post('user/edit', 'adminapi/admin.User/edit');
    Route::post('user/delete', 'adminapi/admin.User/delete');

    // 菜单管理
    Route::get('menu/list', 'adminapi/admin.Menu/list');
    Route::get('menu/tree', 'adminapi/admin.Menu/tree');
    Route::get('menu/nav', 'adminapi/admin.Menu/nav');
    Route::post('menu/add', 'adminapi/admin.Menu/add');
    Route::post('menu/edit', 'adminapi/admin.Menu/edit');
    Route::post('menu/delete', 'adminapi/admin.Menu/delete');

    // 系统配置
    Route::get('config/lists', 'adminapi/admin.SystemConfig/lists');
    Route::post('config/save', 'adminapi/admin.SystemConfig/save');

    // 文件上传
    Route::post('upload/image', 'adminapi/Upload/image');
    Route::post('upload/file', 'adminapi/Upload/file');
    Route::get('upload/lists', 'adminapi/Upload/lists');
    Route::post('upload/delete', 'adminapi/Upload/delete');

    // 消息通知
    Route::get('notice/lists', 'adminapi/Notice/lists');
    Route::get('notice/detail', 'adminapi/Notice/detail');
    Route::post('notice/send', 'adminapi/Notice/send');
    Route::post('notice/edit', 'adminapi/Notice/edit');
    Route::post('notice/delete', 'adminapi/Notice/delete');
    Route::post('notice/read', 'adminapi/Notice/read');
    Route::get('notice/unread_count', 'adminapi/Notice/unreadCount');

    // 定时任务
    Route::get('crontab/lists', 'adminapi/Crontab/lists');
    Route::get('crontab/detail', 'adminapi/Crontab/detail');
    Route::post('crontab/add', 'adminapi/Crontab/add');
    Route::post('crontab/edit', 'adminapi/Crontab/edit');
    Route::post('crontab/delete', 'adminapi/Crontab/delete');
    Route::post('crontab/execute', 'adminapi/Crontab/execute');
    Route::post('crontab/pause', 'adminapi/Crontab/pause');
    Route::post('crontab/resume', 'adminapi/Crontab/resume');
    Route::post('crontab/toggle_status', 'adminapi/Crontab/toggleStatus');
    Route::get('crontab/log_lists', 'adminapi/Crontab/logLists');
    Route::post('crontab/clear_logs', 'adminapi/Crontab/clearLogs');

    // 数据可视化大屏
    Route::get('screen/lists', 'adminapi/screen.Screen/list');
    Route::get('screen/detail', 'adminapi/screen.Screen/detail');
    Route::post('screen/create', 'adminapi/screen.Screen/create');
    Route::post('screen/update', 'adminapi/screen.Screen/update');
    Route::post('screen/delete', 'adminapi/screen.Screen/delete');
    Route::post('screen/save_config', 'adminapi/screen.Screen/saveConfig');
    Route::post('screen/add_component', 'adminapi/screen.Screen/addComponent');
    Route::post('screen/update_component', 'adminapi/screen.Screen/updateComponent');
    Route::post('screen/delete_component', 'adminapi/screen.Screen/deleteComponent');
    Route::post('screen/set_status', 'adminapi/screen.Screen/setStatus');

    // 在线表单设计
    Route::get('form/lists', 'adminapi/Form/lists');
    Route::get('form/info', 'adminapi/Form/info');
    Route::post('form/add', 'adminapi/Form/add');
    Route::post('form/edit', 'adminapi/Form/edit');
    Route::post('form/delete', 'adminapi/Form/delete');
    Route::post('form/toggle_status', 'adminapi/Form/toggleStatus');
    Route::get('form/data_list', 'adminapi/Form/dataList');
    Route::post('form/submit_data', 'adminapi/Form/submitData');
    Route::post('form/delete_data', 'adminapi/Form/deleteData');

    // 租户管理
    Route::get('tenant/lists', 'adminapi/tenant.Tenant/lists');
    Route::get('tenant/info', 'adminapi/tenant.Tenant/info');
    Route::post('tenant/add', 'adminapi/tenant.Tenant/add');
    Route::post('tenant/edit', 'adminapi/tenant.Tenant/edit');
    Route::post('tenant/delete', 'adminapi/tenant.Tenant/delete');
    Route::post('tenant/status', 'adminapi/tenant.Tenant/status');

    // 租户套餐管理
    Route::get('tenant/package_lists', 'adminapi/tenant.Tenant/packageLists');
    Route::get('tenant/package_info', 'adminapi/tenant.Tenant/packageInfo');
    Route::post('tenant/package_add', 'adminapi/tenant.Tenant/packageAdd');
    Route::post('tenant/package_edit', 'adminapi/tenant.Tenant/packageEdit');
    Route::post('tenant/package_delete', 'adminapi/tenant.Tenant/packageDelete');

    // 代码生成器 - 数据库配置
    Route::get('generator/config_lists', 'adminapi/generator.Generator/configLists');
    Route::get('generator/config_info', 'adminapi/generator.Generator/configInfo');
    Route::post('generator/config_add', 'adminapi/generator.Generator/configAdd');
    Route::post('generator/config_edit', 'adminapi/generator.Generator/configEdit');
    Route::post('generator/config_delete', 'adminapi/generator.Generator/configDelete');
    Route::post('generator/test_connection', 'adminapi/generator.Generator/testConnection');

    // 代码生成器 - 模板管理
    Route::get('generator/template_lists', 'adminapi/generator.Generator/templateLists');
    Route::get('generator/template_info', 'adminapi/generator.Generator/templateInfo');
    Route::post('generator/template_add', 'adminapi/generator.Generator/templateAdd');
    Route::post('generator/template_edit', 'adminapi/generator.Generator/templateEdit');
    Route::post('generator/template_delete', 'adminapi/generator.Generator/templateDelete');

    // 代码生成器 - 表结构
    Route::get('generator/table_lists', 'adminapi/generator.Generator/tableLists');
    Route::get('generator/table_columns', 'adminapi/generator.Generator/tableColumns');
    Route::get('generator/gen_types', 'adminapi/generator.Generator/genTypes');
    Route::get('generator/preview', 'adminapi/generator.Generator/preview');
    Route::post('generator/generate', 'adminapi/generator.Generator/generate');

    // 工作流管理
    Route::get('workflow/lists', 'adminapi/workflow.Workflow/lists');
    Route::get('workflow/detail', 'adminapi/workflow.Workflow/detail');
    Route::post('workflow/add', 'adminapi/workflow.Workflow/add');
    Route::post('workflow/edit', 'adminapi/workflow.Workflow/edit');
    Route::post('workflow/delete', 'adminapi/workflow.Workflow/delete');
    Route::post('workflow/publish', 'adminapi/workflow.Workflow/publish');
    Route::post('workflow/unpublish', 'adminapi/workflow.Workflow/unpublish');
    Route::post('workflow/toggle_status', 'adminapi/workflow.Workflow/toggleStatus');

    // 工作流实例
    Route::get('workflow/instance_lists', 'adminapi/workflow.Workflow/instanceLists');
    Route::get('workflow/instance_detail', 'adminapi/workflow.Workflow/instanceDetail');
    Route::get('workflow/todo_list', 'adminapi/workflow.Workflow/todoList');
    Route::post('workflow/start', 'adminapi/workflow.Workflow/start');
    Route::post('workflow/approve', 'adminapi/workflow.Workflow/approve');
    Route::post('workflow/withdraw', 'adminapi/workflow.Workflow/withdraw');
    Route::get('workflow/instance_history', 'adminapi/workflow.Workflow/instanceHistory');

    // 支付配置
    Route::get('pay/config/lists', 'adminapi/pay.PayConfig/lists');
    Route::get('pay/config/info', 'adminapi/pay.PayConfig/info');
    Route::post('pay/config/save', 'adminapi/pay.PayConfig/save');

    // 支付订单
    Route::get('pay/order/lists', 'adminapi/pay.PayOrder/lists');
    Route::get('pay/order/detail', 'adminapi/pay.PayOrder/detail');
    Route::post('pay/order/close', 'adminapi/pay.PayOrder/close');
    Route::post('pay/order/manualPaid', 'adminapi/pay.PayOrder/manualPaid');

    // 退款管理
    Route::get('pay/refund/lists', 'adminapi/pay.PayRefund/lists');
    Route::get('pay/refund/detail', 'adminapi/pay.PayRefund/detail');
    Route::post('pay/refund/apply', 'adminapi/pay.PayRefund/apply');

    // 分账管理
    Route::get('pay/statement/lists', 'adminapi/pay.PayStatement/lists');
    Route::get('pay/statement/detail', 'adminapi/pay.PayStatement/detail');
    Route::post('pay/statement/create', 'adminapi/pay.PayStatement/create');
    Route::get('pay/statement/getAvailableAmount', 'adminapi/pay.PayStatement/getAvailableAmount');
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);
