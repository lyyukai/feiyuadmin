-- ============================================
-- 飞鱼后台管理系统 完整安装SQL (feiyuadmin_install.sql)
-- 版本: 1.0.0
-- 生成时间: 2026-04-07
-- 表前缀: {PREFIX} (安装时替换)
-- ============================================

-- 1. 管理员表 sys_user
CREATE TABLE IF NOT EXISTS `{PREFIX}user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(32) NOT NULL DEFAULT '' COMMENT '密码盐值',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `email` varchar(100) NULL COMMENT '邮箱',
  `mobile` varchar(20) NULL COMMENT '手机号',
  `avatar` varchar(255) NULL COMMENT '头像',
  `dept_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门ID',
  `post_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '岗位ID',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `login_ip` varchar(50) NULL COMMENT '最后登录IP',
  `login_time` datetime NULL COMMENT '最后登录时间',
  `remark` text NULL COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_dept_id` (`dept_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- 2. 角色表 sys_role
CREATE TABLE IF NOT EXISTS `{PREFIX}role` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '角色代码',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `data_scope` varchar(50) NOT NULL DEFAULT 'all' COMMENT '数据范围: all=全部, dept=本部门, self=本人',
  `remark` text NULL COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色表';

-- 3. 菜单表 sys_menu
CREATE TABLE IF NOT EXISTS `{PREFIX}menu` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路由路径',
  `component` varchar(255) NULL COMMENT '组件路径',
  `redirect` varchar(255) NULL COMMENT '重定向路径',
  `icon` varchar(50) NULL COMMENT '菜单图标',
  `menu_type` varchar(10) NOT NULL DEFAULT 'menu' COMMENT '类型: menu=菜单, iframe=iframe, link=外链, button=按钮',
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否隐藏: 0=显示, 1=隐藏',
  `is_full` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否全屏: 0=否, 1=是',
  `is_cache` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否缓存: 0=否, 1=是',
  `permission` varchar(100) NULL COMMENT '权限标识',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `remark` varchar(255) NULL COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`pid`),
  KEY `idx_path` (`path`(100)),
  KEY `idx_permission` (`permission`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='菜单表';

-- 4. 部门表 sys_dept
CREATE TABLE IF NOT EXISTS `{PREFIX}dept` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `leader` varchar(50) NULL COMMENT '负责人',
  `mobile` varchar(20) NULL COMMENT '联系电话',
  `email` varchar(100) NULL COMMENT '邮箱',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`pid`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='部门表';

-- 5. 岗位表 sys_post
CREATE TABLE IF NOT EXISTS `{PREFIX}post` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '岗位ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '岗位名称',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '岗位代码',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `remark` text NULL COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_code` (`code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='岗位表';

-- 6. 系统配置表 sys_config
CREATE TABLE IF NOT EXISTS `{PREFIX}config` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '配置名称',
  `group` varchar(50) NOT NULL DEFAULT 'basic' COMMENT '配置分组',
  `key` varchar(100) NOT NULL DEFAULT '' COMMENT '配置键',
  `value` text NULL COMMENT '配置值',
  `type` varchar(50) NOT NULL DEFAULT 'text' COMMENT '类型: text, textarea, password, number, radio, checkbox, select, switch, json',
  `options` text NULL COMMENT '选项JSON(用于radio/checkbox/select等)',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) NULL COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`key`),
  KEY `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- 7. 角色菜单关联表 sys_role_menu
CREATE TABLE IF NOT EXISTS `{PREFIX}role_menu` (
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `menu_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单ID',
  PRIMARY KEY (`role_id`, `menu_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色菜单关联表';

-- 8. 用户角色关联表 sys_user_role
CREATE TABLE IF NOT EXISTS `{PREFIX}user_role` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  PRIMARY KEY (`user_id`, `role_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户角色关联表';

-- 9. 操作日志表 sys_log
CREATE TABLE IF NOT EXISTS `{PREFIX}log` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `username` varchar(50) NULL COMMENT '用户名',
  `method` varchar(10) NOT NULL COMMENT '请求方法',
  `url` varchar(500) NOT NULL COMMENT '请求地址',
  `ip` varchar(50) NULL COMMENT 'IP地址',
  `location` varchar(255) NULL COMMENT '操作地点',
  `user_agent` varchar(500) NULL COMMENT 'UserAgent',
  `param` text NULL COMMENT '请求参数',
  `result` text NULL COMMENT '返回结果',
  `error` text NULL COMMENT '错误信息',
  `duration` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '执行时长(ms)',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_create_time` (`create_time`),
  KEY `idx_url` (`url`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表';

-- 10. 登录日志表 sys_login_log
CREATE TABLE IF NOT EXISTS `{PREFIX}login_log` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `status` varchar(20) NOT NULL DEFAULT '' COMMENT '状态: success=成功, fail=失败',
  `ip` varchar(50) NULL COMMENT 'IP地址',
  `location` varchar(255) NULL COMMENT '登录地点',
  `user_agent` varchar(500) NULL COMMENT 'UserAgent',
  `msg` varchar(255) NULL COMMENT '提示信息',
  `login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登录时间',
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_login_time` (`login_time`),
  KEY `idx_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录日志表';

-- 11. 数据字典类型表 sys_dict_type
CREATE TABLE IF NOT EXISTS `{PREFIX}dict_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '字典ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '字典名称',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '字典类型',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `remark` varchar(255) NULL COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_type` (`type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='数据字典类型表';

-- 12. 数据字典数据表 sys_dict_data
CREATE TABLE IF NOT EXISTS `{PREFIX}dict_data` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '字典数据ID',
  `dict_type` varchar(50) NOT NULL DEFAULT '' COMMENT '字典类型',
  `label` varchar(100) NOT NULL DEFAULT '' COMMENT '字典标签',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字典值',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `remark` varchar(255) NULL COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_dict_type` (`dict_type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='数据字典数据表';

-- 13. 文件管理表 sys_file
CREATE TABLE IF NOT EXISTS `{PREFIX}file` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `original` varchar(255) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `type` varchar(20) NOT NULL DEFAULT 'file' COMMENT '类型: image=图片, video=视频, audio=音频, file=文件',
  `size` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小(字节)',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '存储路径',
  `url` varchar(500) NULL COMMENT '访问URL',
  `extension` varchar(20) NULL COMMENT '文件扩展名',
  `mime_type` varchar(100) NULL COMMENT 'MIME类型',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传用户ID',
  `storage` varchar(50) NOT NULL DEFAULT 'local' COMMENT '存储方式: local=本地, oss=阿里云, cos=腾讯云, qiniu=七牛云',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '上传时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `delete_time` datetime NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文件管理表';

-- ============================================
-- 初始数据
-- ============================================

-- 插入超级管理员角色
INSERT INTO `{PREFIX}role` (`name`, `code`, `status`, `sort`, `data_scope`, `remark`, `create_time`, `update_time`) VALUES
('超级管理员', 'super', 1, 1, 'all', '拥有系统所有权限', NOW(), NOW());

-- 插入普通角色
INSERT INTO `{PREFIX}role` (`name`, `code`, `status`, `sort`, `data_scope`, `remark`, `create_time`, `update_time`) VALUES
('运营人员', 'operator', 1, 2, 'dept', '运营人员角色', NOW(), NOW());

-- 插入默认部门
INSERT INTO `{PREFIX}dept` (`name`, `pid`, `path`, `status`, `sort`, `create_time`, `update_time`) VALUES
('总公司', 0, '0', 1, 1, NOW(), NOW());

-- 插入子部门示例
INSERT INTO `{PREFIX}dept` (`name`, `pid`, `path`, `leader`, `mobile`, `email`, `status`, `sort`, `create_time`, `update_time`) VALUES
('技术部', 1, '0,1', '张三', '13800138000', 'tech@example.com', 1, 1, NOW(), NOW()),
('运营部', 1, '0,1', '李四', '13800138001', 'ops@example.com', 1, 2, NOW(), NOW());

-- 插入默认岗位
INSERT INTO `{PREFIX}post` (`name`, `code`, `status`, `sort`, `remark`, `create_time`, `update_time`) VALUES
('系统管理员', 'admin', 1, 1, '系统管理岗位', NOW(), NOW()),
('部门经理', 'manager', 1, 2, '部门管理岗位', NOW(), NOW()),
('普通员工', 'staff', 1, 3, '普通员工岗位', NOW(), NOW());

-- 插入默认菜单
INSERT INTO `{PREFIX}menu` (`name`, `pid`, `path`, `component`, `redirect`, `icon`, `menu_type`, `sort`, `status`, `create_time`, `update_time`) VALUES
('工作台', 0, '/dashboard', 'dashboard/index', '', 'Odometer', 'menu', 1, 1, NOW(), NOW());

INSERT INTO `{PREFIX}menu` (`name`, `pid`, `path`, `component`, `icon`, `menu_type`, `sort`, `status`, `create_time`, `update_time`) VALUES
('系统管理', 0, '/system', '', 'Setting', 'menu', 10, 1, NOW(), NOW()),
('用户管理', 2, '/system/user', 'system/user/index', 'User', 'menu', 1, 1, NOW(), NOW()),
('角色管理', 2, '/system/role', 'system/role/index', 'Key', 'menu', 2, 1, NOW(), NOW()),
('菜单管理', 2, '/system/menu', 'system/menu/index', 'Menu', 'menu', 3, 1, NOW(), NOW()),
('部门管理', 2, '/system/dept', 'system/dept/index', 'Office', 'menu', 4, 1, NOW(), NOW()),
('岗位管理', 2, '/system/post', 'system/post/index', 'Postcard', 'menu', 5, 1, NOW(), NOW()),
('系统配置', 2, '/system/config', 'system/config/index', 'Tools', 'menu', 6, 1, NOW(), NOW());

-- 插入系统配置
INSERT INTO `{PREFIX}config` (`name`, `group`, `key`, `value`, `type`, `sort`, `remark`, `create_time`, `update_time`) VALUES
('网站名称', 'basic', 'site_name', '飞鱼后台管理系统', 'text', 1, '网站名称', NOW(), NOW()),
('网站Logo', 'basic', 'site_logo', '', 'text', 2, '网站Logo地址', NOW(), NOW()),
('网站描述', 'basic', 'site_description', '基于Vue3+ThinkPHP8的高性能通用后台管理框架', 'textarea', 3, '网站描述', NOW(), NOW()),
('ICP备案号', 'basic', 'icp', '', 'text', 4, 'ICP备案号', NOW(), NOW()),
('Copyright', 'basic', 'copyright', '© 2026 飞鱼科技', 'text', 5, '版权信息', NOW(), NOW()),
('安装状态', 'basic', 'installed', '1', 'text', 0, '系统安装标记，勿删', NOW(), NOW());

-- 插入默认字典类型
INSERT INTO `{PREFIX}dict_type` (`name`, `type`, `status`, `remark`, `create_time`, `update_time`) VALUES
('用户状态', 'user_status', 1, '用户状态字典', NOW(), NOW()),
('菜单状态', 'menu_status', 1, '菜单状态字典', NOW(), NOW()),
('部门状态', 'dept_status', 1, '部门状态字典', NOW(), NOW()),
('岗位状态', 'post_status', 1, '岗位状态字典', NOW(), NOW()),
('角色状态', 'role_status', 1, '角色状态字典', NOW(), NOW()),
('系统状态', 'sys_status', 1, '系统状态字典', NOW(), NOW()),
('数据范围', 'data_scope', 1, '数据权限范围字典', NOW(), NOW());

-- 插入默认字典数据
INSERT INTO `{PREFIX}dict_data` (`dict_type`, `label`, `value`, `sort`, `status`, `create_time`, `update_time`) VALUES
('user_status', '正常', '1', 1, 1, NOW(), NOW()),
('user_status', '禁用', '0', 2, 1, NOW(), NOW()),
('menu_status', '正常', '1', 1, 1, NOW(), NOW()),
('menu_status', '禁用', '0', 2, 1, NOW(), NOW()),
('dept_status', '正常', '1', 1, 1, NOW(), NOW()),
('dept_status', '禁用', '0', 2, 1, NOW(), NOW()),
('post_status', '正常', '1', 1, 1, NOW(), NOW()),
('post_status', '禁用', '0', 2, 1, NOW(), NOW()),
('role_status', '正常', '1', 1, 1, NOW(), NOW()),
('role_status', '禁用', '0', 2, 1, NOW(), NOW()),
('sys_status', '启用', '1', 1, 1, NOW(), NOW()),
('sys_status', '禁用', '0', 2, 1, NOW(), NOW()),
('data_scope', '全部数据', 'all', 1, 1, NOW(), NOW()),
('data_scope', '本部门数据', 'dept', 2, 1, NOW(), NOW()),
('data_scope', '本人数据', 'self', 3, 1, NOW(), NOW());
