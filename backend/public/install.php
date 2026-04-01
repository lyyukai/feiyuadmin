<?php
/**
 * 飞羽后台管理系统 - 安装向导
 * 独立运行，不依赖框架 autoload
 */

// ============================================================
// 防止直接访问（通过锁文件检测）
// ============================================================
define('IN_INSTALL', true);
$lockFile = __DIR__ . '/install.lock';

if (file_exists($lockFile)) {
    showLocked();
    exit;
}

// ============================================================
// 安装步骤控制
// ============================================================
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

// POST 处理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        // 测试数据库连接
        $result = testDatabase($_POST);
        if ($result !== true) {
            $error = $result;
            $step = 1;
        } else {
            $step = 2;
        }
    } elseif ($step === 3) {
        // 执行安装
        $result = doInstall($_POST);
        if ($result !== true) {
            $error = $result;
            $step = 2;
        } else {
            $step = 3;
        }
    }
}

// ============================================================
// 环境检测
// ============================================================
function checkEnvironment() {
    $checks = [];
    $allPass = true;

    // PHP 版本
    $phpVersion = PHP_VERSION;
    $phpPass = version_compare($phpVersion, '8.0.0', '>=');
    $checks['php_version'] = [
        'name' => 'PHP 版本',
        'current' => $phpVersion,
        'required' => '>= 8.0.0',
        'pass' => $phpPass
    ];
    if (!$phpPass) $allPass = false;

    // PDO 扩展
    $pdoPass = extension_loaded('pdo') && extension_loaded('pdo_mysql');
    $checks['pdo'] = [
        'name' => 'PDO MySQL 扩展',
        'current' => $pdoPass ? '已开启' : '未开启',
        'required' => '必须',
        'pass' => $pdoPass
    ];
    if (!$pdoPass) $allPass = false;

    // fileinfo 扩展
    $fileinfoPass = extension_loaded('fileinfo');
    $checks['fileinfo'] = [
        'name' => 'fileinfo 扩展',
        'current' => $fileinfoPass ? '已开启' : '未开启',
        'required' => '必须',
        'pass' => $fileinfoPass
    ];
    if (!$fileinfoPass) $allPass = false;

    // curl 扩展
    $curlPass = extension_loaded('curl');
    $checks['curl'] = [
        'name' => 'cURL 扩展',
        'current' => $curlPass ? '已开启' : '未开启',
        'required' => '必须',
        'pass' => $curlPass
    ];
    if (!$curlPass) $allPass = false;

    // mbstring 扩展
    $mbstringPass = extension_loaded('mbstring');
    $checks['mbstring'] = [
        'name' => 'mbstring 扩展',
        'current' => $mbstringPass ? '已开启' : '未开启',
        'required' => '必须',
        'pass' => $mbstringPass
    ];
    if (!$mbstringPass) $allPass = false;

    // 目录权限检测
    $basePath = dirname(__DIR__);
    $configDir = $basePath . '/config';
    $runtimeDir = $basePath . '/runtime';

    $dirs = [
        'config' => $configDir,
        'runtime' => $runtimeDir,
    ];

    foreach ($dirs as $key => $dir) {
        $writable = is_dir($dir) && is_writable($dir);
        $checks['dir_' . $key] = [
            'name' => $key . ' 目录可写',
            'current' => $writable ? '可写' : (is_dir($dir) ? '不可写' : '不存在'),
            'required' => '必须',
            'pass' => $writable
        ];
        if (!$writable) $allPass = false;
    }

    return ['checks' => $checks, 'allPass' => $allPass];
}

// ============================================================
// 数据库连接测试
// ============================================================
function testDatabase($post) {
    $host = trim($post['db_host']);
    $port = trim($post['db_port']);
    $database = trim($post['db_name']);
    $username = trim($post['db_user']);
    $password = $post['db_pwd'];
    $prefix = trim($post['db_prefix']);

    if (empty($host) || empty($database) || empty($username)) {
        return '请填写完整的数据库信息';
    }

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    } catch (PDOException $e) {
        return '数据库连接失败：' . $e->getMessage();
    }
}

// ============================================================
// 执行安装
// ============================================================
function doInstall($post) {
    $host = trim($post['db_host']);
    $port = trim($post['db_port']);
    $database = trim($post['db_name']);
    $username = trim($post['db_user']);
    $password = $post['db_pwd'];
    $prefix = trim($post['db_prefix']);
    $adminUser = trim($post['admin_user']);
    $adminPwd = $post['admin_pwd'];
    $adminPwd2 = $post['admin_pwd2'];

    // 验证管理员密码
    if (empty($adminUser) || empty($adminPwd) || empty($adminPwd2)) {
        return '请填写完整的管理员信息';
    }
    if ($adminPwd !== $adminPwd2) {
        return '两次输入的密码不一致';
    }
    if (strlen($adminPwd) < 6) {
        return '密码长度不能少于6位';
    }

    try {
        // 1. 连接数据库
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. 生成加密盐值
        $salt = bin2hex(random_bytes(16));
        $encryptedPwd = md5($salt . $adminPwd);

        // 3. 创建 sys_user 表
        $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}user` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
            `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
            `salt` varchar(32) NOT NULL DEFAULT '' COMMENT '盐值',
            `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
            `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
            `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
            `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
            `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0禁用',
            `login_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
            `login_ip` varchar(50) NOT NULL DEFAULT '' COMMENT '最后登录IP',
            `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
            `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
            `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表'";
        $pdo->exec($sql);

        // 4. 插入管理员账号
        $now = time();
        $insertSql = "INSERT INTO `{$prefix}user` 
            (`username`, `password`, `salt`, `nickname`, `status`, `create_time`, `update_time`) 
            VALUES 
            (:username, :password, :salt, :nickname, 1, :create_time, :update_time)";
        $stmt = $pdo->prepare($insertSql);
        $stmt->execute([
            ':username' => $adminUser,
            ':password' => $encryptedPwd,
            ':salt' => $salt,
            ':nickname' => '管理员',
            ':create_time' => $now,
            ':update_time' => $now,
        ]);

        // 5. 写入 database.php 配置
        $configPath = dirname(__DIR__) . '/config/database.php';
        $configContent = "<?php
// Database configuration - Generated by installer
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'type' => 'mysql',
            'hostname' => '{$host}',
            'database' => '{$database}',
            'username' => '{$username}',
            'password' => '{$password}',
            'hostport' => '{$port}',
            'params' => [],
            'charset' => 'utf8mb4',
            'prefix' => '{$prefix}',
            'deploy' => 0,
            'rw_separate' => false,
            'master_num' => 1,
            'slave_no' => '',
            'fields_strict' => true,
            'break_reconnect' => false,
            'trigger_sql' => true,
            'fields_cache' => false,
        ],
    ],
];
";
        if (file_put_contents($configPath, $configContent) === false) {
            return '配置文件写入失败，请检查 config 目录权限';
        }

        // 6. 创建 install.lock
        $lockFile = __DIR__ . '/install.lock';
        file_put_contents($lockFile, date('Y-m-d H:i:s'));

        return true;

    } catch (PDOException $e) {
        return '安装失败：' . $e->getMessage();
    } catch (Exception $e) {
        return '安装异常：' . $e->getMessage();
    }
}

// ============================================================
// 显示已安装页面
// ============================================================
function showLocked() {
    echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统已安装 - 飞羽后台管理系统</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .locked-box {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .locked-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .locked-icon svg { width: 40px; height: 40px; fill: #fff; }
        h1 { font-size: 24px; color: #333; margin-bottom: 12px; }
        p { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: opacity 0.3s;
        }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="locked-box">
        <div class="locked-icon">
            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
        </div>
        <h1>系统已安装</h1>
        <p>飞羽后台管理系统已经完成安装，如需重新安装请删除根目录下的 <strong>install.lock</strong> 文件，然后重新访问安装向导。</p>
        <a href="admin.php" class="btn">进入后台管理</a>
    </div>
</body>
</html>';
}

// ============================================================
// 渲染安装向导 HTML
// ============================================================
function renderInstaller($step, $error, $checks = null, $postData = []) {
    $stepTitles = ['', '环境检测', '数据库配置', '管理员设置', '安装完成'];
    $title = $stepTitles[$step] ?? '安装向导';
    $progress = $step * 25;

    // 错误提示 HTML
    $errorHtml = $error ? '<div class="error-msg"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>' . htmlspecialchars($error) . '</div>' : '';

    // 步骤1：环境检测
    if ($step === 1) {
        $checksHtml = '';
        foreach ($checks['checks'] as $check) {
            $statusIcon = $check['pass'] 
                ? '<svg viewBox="0 0 24 24" class="icon-pass"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' 
                : '<svg viewBox="0 0 24 24" class="icon-fail"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
            $rowClass = $check['pass'] ? 'pass' : 'fail';
            $checksHtml .= "<tr class=\"{$rowClass}\">
                <td>{$check['name']}</td>
                <td>{$check['current']}</td>
                <td>{$check['required']}</td>
                <td>{$statusIcon}</td>
            </tr>";
        }

        $nextBtn = $checks['allPass'] 
            ? '<a href="?step=2" class="btn btn-primary">下一步：配置数据库</a>'
            : '<button class="btn btn-disabled" disabled>请修复以上问题后继续</button>';

        $content = "
        <div class=\"card\">
            <h3 class=\"card-title\">服务器环境检测</h3>
            <table class=\"check-table\">
                <thead><tr><th>检测项</th><th>当前状态</th><th>要求</th><th>状态</th></tr></thead>
                <tbody>{$checksHtml}</tbody>
            </table>
        </div>
        <div class=\"action-row\">
            {$nextBtn}
        </div>";

    // 步骤2：数据库配置
    } elseif ($step === 2) {
        $content = "
        <form method=\"post\" action=\"?step=3\">
            <input type=\"hidden\" name=\"step\" value=\"2\">
            <div class=\"card\">
                <h3 class=\"card-title\">数据库配置</h3>
                <div class=\"form-grid\">
                    <div class=\"form-group\">
                        <label>数据库主机</label>
                        <input type=\"text\" name=\"db_host\" value=\"" . ($postData['db_host'] ?? '127.0.0.1') . "\" placeholder=\"127.0.0.1\" required>
                    </div>
                    <div class=\"form-group\">
                        <label>端口号</label>
                        <input type=\"number\" name=\"db_port\" value=\"" . ($postData['db_port'] ?? '3306') . "\" placeholder=\"3306\" required>
                    </div>
                    <div class=\"form-group full\">
                        <label>数据库名</label>
                        <input type=\"text\" name=\"db_name\" value=\"" . ($postData['db_name'] ?? 'feiyuadmin') . "\" placeholder=\"请输入数据库名\" required>
                    </div>
                    <div class=\"form-group\">
                        <label>数据库用户名</label>
                        <input type=\"text\" name=\"db_user\" value=\"" . ($postData['db_user'] ?? 'root') . "\" placeholder=\"root\" required>
                    </div>
                    <div class=\"form-group\">
                        <label>数据库密码</label>
                        <input type=\"password\" name=\"db_pwd\" value=\"" . ($postData['db_pwd'] ?? '') . "\" placeholder=\"请输入密码\">
                    </div>
                    <div class=\"form-group full\">
                        <label>表前缀</label>
                        <input type=\"text\" name=\"db_prefix\" value=\"" . ($postData['db_prefix'] ?? 'sys_') . "\" placeholder=\"sys_\">
                        <span class=\"hint\">建议使用带下划线的前缀，如 sys_</span>
                    </div>
                </div>
            </div>
            <div class=\"action-row\">
                <a href=\"?step=1\" class=\"btn btn-secondary\">上一步</a>
                <button type=\"submit\" class=\"btn btn-primary\">下一步：创建管理员</button>
            </div>
        </form>";

    // 步骤3：管理员设置
    } elseif ($step === 3) {
        // 从 step2 的 post 数据中提取 db 配置
        $dbFields = '';
        foreach (['db_host', 'db_port', 'db_name', 'db_user', 'db_pwd', 'db_prefix'] as $f) {
            $v = isset($_POST[$f]) ? htmlspecialchars($_POST[$f]) : '';
            $dbFields .= '<input type="hidden" name="'.$f.'" value="'.$v.'">';
        }

        $content = "
        <form method=\"post\" action=\"?step=4\" onsubmit=\"return validateForm()\">
            {$dbFields}
            <div class=\"card\">
                <h3 class=\"card-title\">创建管理员账号</h3>
                <div class=\"form-grid\">
                    <div class=\"form-group full\">
                        <label>管理员用户名</label>
                        <input type=\"text\" name=\"admin_user\" id=\"admin_user\" value=\"admin\" placeholder=\"请输入管理员用户名\" required>
                    </div>
                    <div class=\"form-group\">
                        <label>设置密码</label>
                        <input type=\"password\" name=\"admin_pwd\" id=\"admin_pwd\" placeholder=\"请输入密码（至少6位）\" required minlength=\"6\">
                    </div>
                    <div class=\"form-group\">
                        <label>确认密码</label>
                        <input type=\"password\" name=\"admin_pwd2\" id=\"admin_pwd2\" placeholder=\"请再次输入密码\" required minlength=\"6\">
                    </div>
                </div>
            </div>
            <div class=\"action-row\">
                <a href=\"?step=2\" class=\"btn btn-secondary\">上一步</a>
                <button type=\"submit\" class=\"btn btn-primary\">开始安装</button>
            </div>
        </form>";

    // 步骤4：完成
    } elseif ($step === 4) {
        $content = "
        <div class=\"card success-card\">
            <div class=\"success-icon\">
                <svg viewBox=\"0 0 24 24\"><path d=\"M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z\"/></svg>
            </div>
            <h3>安装成功！</h3>
            <p>飞羽后台管理系统已安装完成，请妥善保管您的管理员账号信息。</p>
            <div class=\"account-info\">
                <div class=\"info-row\"><span>用户名：</span><strong>admin</strong> <span class=\"badge\">可自行修改</span></div>
                <div class=\"info-row\"><span>密码：</span><strong>刚才设置的管理员密码</strong></div>
            </div>
        </div>
        <div class=\"action-row\">
            <a href=\"admin.php\" class=\"btn btn-primary\">立即进入后台</a>
        </div>";
    }

    // 组装完整页面
    echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . ' - 飞羽后台管理系统安装向导</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .installer-wrapper {
            max-width: 720px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .header h1 {
            color: #fff;
            font-size: 28px;
            font-weight: 600;
            text-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .header p {
            color: rgba(255,255,255,0.8);
            margin-top: 8px;
            font-size: 14px;
        }
        .progress-bar {
            background: rgba(255,255,255,0.2);
            border-radius: 50px;
            height: 8px;
            margin-bottom: 32px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #fff;
            border-radius: 50px;
            transition: width 0.5s ease;
        }
        .step-indicators {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .step-indicator {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
        }
        .step-indicator.active { color: #fff; font-weight: 600; }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 16px;
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eee;
        }
        .check-table {
            width: 100%;
            border-collapse: collapse;
        }
        .check-table th, .check-table td {
            text-align: left;
            padding: 12px 8px;
            font-size: 14px;
        }
        .check-table th {
            color: #888;
            font-weight: 500;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .check-table tr.pass td { color: #333; }
        .check-table tr.fail td { color: #f56c6c; }
        .icon-pass { width: 20px; height: 20px; fill: #67c23a; vertical-align: middle; }
        .icon-fail { width: 20px; height: 20px; fill: #f56c6c; vertical-align: middle; }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label {
            font-size: 14px;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .form-group input {
            padding: 10px 14px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        .form-group .hint {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
        .error-msg {
            background: #fef0f0;
            border: 1px solid #fde2e2;
            color: #f56c6c;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .error-msg svg { width: 18px; height: 18px; fill: #f56c6c; flex-shrink: 0; }
        .action-row {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 8px;
        }
        .btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-secondary {
            background: #f5f5f5;
            color: #666;
        }
        .btn-secondary:hover { background: #e8e8e8; }
        .btn-disabled {
            background: #ccc;
            color: #fff;
            cursor: not-allowed;
        }
        .success-card { text-align: center; }
        .success-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #67c23a, #85ce61);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .success-icon svg { width: 32px; height: 32px; fill: #fff; }
        .success-card h3 { font-size: 20px; color: #333; margin-bottom: 8px; }
        .success-card p { color: #666; font-size: 14px; margin-bottom: 20px; }
        .account-info {
            background: #f5f7fa;
            border-radius: 8px;
            padding: 16px 20px;
            text-align: left;
            margin-top: 12px;
        }
        .info-row { font-size: 14px; color: #333; margin-bottom: 8px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-row span { color: #888; }
        .info-row strong { color: #333; }
        .badge {
            background: #667eea;
            color: #fff;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
            vertical-align: middle;
        }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: 1; }
        }
    </style>
</head>
<body>
<div class="installer-wrapper">
    <div class="header">
        <h1>飞羽后台管理系统</h1>
        <p>安装向导</p>
    </div>
    <div class="progress-bar">
        <div class="progress-fill" style="width:' . $progress . '%"></div>
    </div>
    <div class="step-indicators">
        <span class="step-indicator ' . ($step >= 1 ? 'active' : '') . '">1 环境检测</span>
        <span class="step-indicator ' . ($step >= 2 ? 'active' : '') . '">2 数据库配置</span>
        <span class="step-indicator ' . ($step >= 3 ? 'active' : '') . '">3 管理员设置</span>
        <span class="step-indicator ' . ($step >= 4 ? 'active' : '') . '">4 安装完成</span>
    </div>
    ' . $errorHtml . '
    ' . $content . '
</div>
<script>
function validateForm() {
    var pwd = document.getElementById("admin_pwd").value;
    var pwd2 = document.getElementById("admin_pwd2").value;
    if (pwd.length < 6) {
        alert("密码长度不能少于6位");
        return false;
    }
    if (pwd !== pwd2) {
        alert("两次输入的密码不一致");
        return false;
    }
    return true;
}
</script>
</body>
</html>';
}

// ============================================================
// 引导渲染
// ============================================================
if ($step === 1) {
    $env = checkEnvironment();
    renderInstaller(1, $error, $env);
} elseif ($step === 2) {
    renderInstaller(2, $error, null, $_POST);
} elseif ($step === 3) {
    renderInstaller(3, $error, null, $_POST);
} elseif ($step === 4) {
    renderInstaller(4, $error);
}
