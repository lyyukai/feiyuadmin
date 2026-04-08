<?php
/**
 * 飞鱼后台管理系统 - 安装控制器
 * 提供安装向导的后端API接口
 */

declare(strict_types=1);

namespace app\api\controller;

use app\common\controller\BaseController;
use think\Request;
use think\Response;

/**
 * 安装向导接口
 * @package app\api\controller
 */
class InstallController extends BaseController
{
    /** @var array 免登录接口 */
    protected array $notNeedLogin = ['check', 'testdb', 'exec'];

    /**
     * 安装状态检测
     * GET /api/install/check
     * 检查系统是否已完成安装
     */
    public function check(): Response
    {
        $lockFile = dirname(__DIR__, 4) . '/public/install.lock';

        if (file_exists($lockFile)) {
            $lockData = @json_decode(file_get_contents($lockFile), true);
            return $this->success('系统已安装', [
                'installed' => true,
                'version' => $lockData['version'] ?? '1.0.0',
                'installed_at' => $lockData['installed_at'] ?? '',
            ]);
        }

        return $this->success('系统未安装', [
            'installed' => false,
        ]);
    }

    /**
     * 测试数据库连接
     * POST /api/install/testdb
     * @param Request $request
     */
    public function testdb(Request $request): Response
    {
        $host = trim($request->post('db_host', '127.0.0.1'));
        $port = trim($request->post('db_port', '3307'));
        $database = trim($request->post('db_name', ''));
        $username = trim($request->post('db_user', 'root'));
        $password = $request->post('db_pwd', '');

        if (empty($database)) {
            return $this->fail('请填写数据库名');
        }

        $timeout = 5;

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => $timeout,
            ]);

            // 执行一条简单查询验证连接
            $pdo->query('SELECT 1');

            return $this->success('数据库连接成功', [
                'duration' => 0,
            ]);
        } catch (\PDOException $e) {
            $msg = $e->getMessage();

            if (strpos($msg, 'Access denied') !== false) {
                $msg = '用户名或密码错误，请检查数据库账号密码';
            } elseif (strpos($msg, 'Unknown database') !== false) {
                $msg = '数据库不存在，请先创建该数据库';
            } elseif (strpos($msg, 'Connection refused') !== false) {
                $msg = 'MySQL服务未启动或端口被拒绝，请检查MySQL服务状态';
            } elseif (strpos($msg, 'No such file') !== false) {
                $msg = 'MySQL socket文件不存在，请检查MySQL是否正常运行';
            }

            return $this->fail('数据库连接失败：' . $msg);
        }
    }

    /**
     * 执行安装
     * POST /api/install/exec
     * 创建数据库表、写入初始数据、创建管理员账号
     * @param Request $request
     */
    public function exec(Request $request): Response
    {
        $host = trim($request->post('db_host', '127.0.0.1'));
        $port = trim($request->post('db_port', '3307'));
        $database = trim($request->post('db_name', ''));
        $username = trim($request->post('db_user', 'root'));
        $password = $request->post('db_pwd', '');
        $prefix = trim($request->post('db_prefix', 'fy_'));
        $adminUser = trim($request->post('admin_user', ''));
        $adminPwd = $request->post('admin_pwd', '');
        $adminPwd2 = $request->post('admin_pwd2', '');

        // 验证
        if (empty($database)) {
            return $this->fail('请填写数据库名');
        }
        if (empty($adminUser)) {
            return $this->fail('请填写管理员用户名');
        }
        if (strlen($adminUser) < 4 || strlen($adminUser) > 20) {
            return $this->fail('用户名长度需在4-20位之间');
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $adminUser)) {
            return $this->fail('用户名只能包含字母和数字');
        }
        if (empty($adminPwd)) {
            return $this->fail('请填写管理员密码');
        }
        if ($adminPwd !== $adminPwd2) {
            return $this->fail('两次输入的密码不一致');
        }
        if (strlen($adminPwd) < 8) {
            return $this->fail('密码长度不能少于8位');
        }

        try {
            // 1. 连接数据库
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            // 2. 读取并执行安装SQL
            $sqlFile = dirname(__DIR__, 4) . '/database/feiyuadmin_install.sql';
            if (!file_exists($sqlFile)) {
                return $this->fail('安装SQL文件不存在，请检查 database/feiyuadmin_install.sql');
            }

            $sqlContent = file_get_contents($sqlFile);
            $sqlContent = str_replace('{PREFIX}', $prefix, $sqlContent);

            // 分割并执行SQL
            $statements = array_filter(
                array_map('trim', explode(';', $sqlContent)),
                fn($s) => !empty($s) && strpos($s, '--') !== 0 && strpos($s, '/*') !== 0
            );

            $pdo->exec('SET NAMES utf8mb4');
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }

            // 3. 创建管理员账号
            $salt = bin2hex(random_bytes(16));
            // 密码用 password_hash 加密存储
            $hashedPwd = password_hash($adminPwd, PASSWORD_DEFAULT);
            // 同时兼容前端 md5(salt+password) 方式
            $encryptedPwd = md5($salt . $adminPwd);
            $now = date('Y-m-d H:i:s');

            // 检查用户表是否有salt字段
            $hasSalt = false;
            try {
                $cols = $pdo->query("SHOW COLUMNS FROM `{$prefix}user` LIKE 'salt'");
                $hasSalt = $cols->rowCount() > 0;
            } catch (\Exception $e) {
                // 忽略
            }

            if ($hasSalt) {
                $stmt = $pdo->prepare("INSERT INTO `{$prefix}user`
                    (`username`, `password`, `salt`, `nickname`, `status`, `create_time`, `update_time`)
                    VALUES
                    (:username, :password, :salt, :nickname, 1, :create_time, :update_time)");
                $stmt->execute([
                    ':username' => $adminUser,
                    ':password' => $encryptedPwd,
                    ':salt' => $salt,
                    ':nickname' => '管理员',
                    ':create_time' => $now,
                    ':update_time' => $now,
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO `{$prefix}user`
                    (`username`, `password`, `nickname`, `status`, `create_time`, `update_time`)
                    VALUES
                    (:username, :password, :nickname, 1, :create_time, :update_time)");
                $stmt->execute([
                    ':username' => $adminUser,
                    ':password' => $hashedPwd,
                    ':nickname' => '管理员',
                    ':create_time' => $now,
                    ':update_time' => $now,
                ]);
            }

            $adminId = (int) $pdo->lastInsertId();

            // 4. 给管理员分配超级管理员角色
            $pdo->exec("INSERT INTO `{$prefix}user_role` (`user_id`, `role_id`) VALUES ({$adminId}, 1)");

            // 5. 插入安装完成标记到 sys_config
            $pdo->exec("INSERT INTO `{$prefix}config` (`name`, `group`, `key`, `value`, `type`, `sort`, `create_time`, `update_time`)
                VALUES ('installed', 'basic', 'installed', '1', 'text', 0, '{$now}', '{$now}')
                ON DUPLICATE KEY UPDATE `value` = '1'");

            // 6. 写入数据库配置文件
            $configPath = dirname(__DIR__, 4) . '/config/database.php';
            $configContent = "<?php
// Database configuration - Generated by installer at " . date('Y-m-d H:i:s') . "
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
                return $this->fail('配置文件写入失败，请检查 config 目录权限');
            }

            // 7. 创建 install.lock
            $lockData = [
                'version' => '1.0.0',
                'installed_at' => $now,
                'admin_account' => $adminUser,
                'install_hash' => hash('sha256', $adminUser . $salt . $encryptedPwd),
            ];
            $lockFile = dirname(__DIR__, 4) . '/public/install.lock';
            file_put_contents($lockFile, json_encode($lockData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return $this->success('安装成功', [
                'admin_account' => $adminUser,
                'version' => '1.0.0',
            ]);

        } catch (\PDOException $e) {
            return $this->fail('数据库错误：' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->fail('安装异常：' . $e->getMessage());
        }
    }
}
