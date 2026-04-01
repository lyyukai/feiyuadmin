#!/usr/bin/env php
<?php
/**
 * 登录逻辑自动化测试
 */
require __DIR__ . '/vendor/autoload.php';

$app = new \think\App();
$app->initialize();

// 测试用例
$tests = [
    [
        'name' => '测试1: 空用户名',
        'input' => ['username' => '', 'password' => 'admin123'],
        'expect' => 'fail',
    ],
    [
        'name' => '测试2: 不存在的用户',
        'input' => ['username' => 'nonexistent', 'password' => 'admin123'],
        'expect' => 'fail',
    ],
    [
        'name' => '测试3: 错误密码',
        'input' => ['username' => 'admin', 'password' => 'wrongpassword'],
        'expect' => 'fail',
    ],
    [
        'name' => '测试4: 正确登录',
        'input' => ['username' => 'admin', 'password' => 'admin123'],
        'expect' => 'success',
    ],
];

echo "========== 登录测试自动化 ==========\n\n";

$passed = 0;
$failed = 0;

foreach ($tests as $i => $test) {
    echo "【{$test['name']}】\n";
    echo "输入: " . json_encode($test['input'], JSON_UNESCAPED_UNICODE) . "\n";
    
    try {
        // 调用登录逻辑
        $result = \app\adminapi\logic\LoginLogic::login($test['input']);
        
        if ($test['expect'] === 'success' && isset($result['token'])) {
            echo "✅ 结果: 成功获取Token\n";
            echo "Token: " . substr($result['token'], 0, 50) . "...\n";
            $passed++;
        } else {
            echo "❌ 结果: 意外成功，结果: " . json_encode($result) . "\n";
            $failed++;
        }
    } catch (\think\exception\HttpResponseException $e) {
        // 这是ThinkPHP的HTTP响应异常，表示登录失败
        if ($test['expect'] === 'fail') {
            $response = $e->getResponse();
            $content = $response->getContent();
            echo "✅ 结果: 正确拒绝\n";
            $passed++;
        } else {
            echo "❌ 结果: 意外失败\n";
            $failed++;
        }
    } catch (\Exception $e) {
        $msg = method_exists($e, 'getMessage') ? $e->getMessage() : get_class($e);
        if ($test['expect'] === 'fail') {
            echo "✅ 结果: 正确拒绝 - {$msg}\n";
            $passed++;
        } else {
            echo "❌ 结果: 意外失败 - {$msg}\n";
            $failed++;
        }
    }
    echo "\n";
}

echo "========== 测试结果 ==========\n";
echo "通过: $passed\n";
echo "失败: $failed\n";
echo "总计: " . count($tests) . "\n";
echo "状态: " . ($failed === 0 ? "✅ 全部通过" : "❌ 存在失败") . "\n";
