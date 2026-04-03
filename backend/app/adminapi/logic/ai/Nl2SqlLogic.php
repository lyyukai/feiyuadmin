<?php
/**
 * NL2SQL 逻辑层
 * 
 * 快速实现版本：基于关键词匹配的简单 SQL 生成
 * 后续可替换为完整的 AI 语义解析
 */

declare(strict_types=1);

namespace app\adminapi\logic\ai;

class Nl2SqlLogic
{
    /**
     * 自然语言转 SQL
     */
    public function convert(string $prompt): array
    {
        $prompt = trim($prompt);
        $lowerPrompt = mb_strtolower($prompt);

        // 根据关键词生成模拟 SQL（演示用）
        if (str_contains($lowerPrompt, '用户') || str_contains($lowerPrompt, 'user')) {
            return [
                'sql' => "SELECT `id`, `username`, `nickname`, `email`, `status`, `create_time`\nFROM `sys_user`\nWHERE `status` = 1\nORDER BY `create_time` DESC;",
                'explanation' => '查询所有启用状态的用户，按创建时间倒序排列',
                'tables' => ['sys_user'],
            ];
        }

        if (str_contains($lowerPrompt, '角色') || str_contains($lowerPrompt, 'role')) {
            return [
                'sql' => "SELECT `id`, `name`, `code`, `status`, `create_time`\nFROM `sys_role`\nWHERE `status` = 1\nORDER BY `id` ASC;",
                'explanation' => '查询所有启用状态的角色',
                'tables' => ['sys_role'],
            ];
        }

        if (str_contains($lowerPrompt, '订单') || str_contains($lowerPrompt, 'order')) {
            return [
                'sql' => "SELECT `id`, `order_no`, `user_id`, `amount`, `status`, `create_time`\nFROM `pay_order`\nWHERE `status` IN (1, 2)\nORDER BY `create_time` DESC\nLIMIT 100;",
                'explanation' => '查询最近100条进行中的订单',
                'tables' => ['pay_order'],
            ];
        }

        if (str_contains($lowerPrompt, '统计') || str_contains($lowerPrompt, 'count')) {
            return [
                'sql' => "SELECT COUNT(*) AS total_count, \n  DATE_FORMAT(`create_time`, '%Y-%m') AS month\nFROM `sys_user`\nGROUP BY DATE_FORMAT(`create_time`, '%Y-%m')\nORDER BY month DESC;",
                'explanation' => '按月统计用户注册数量',
                'tables' => ['sys_user'],
            ];
        }

        // 默认 SELECT 模板
        return [
            'sql' => "-- 根据您的问题生成的 SQL：\nSELECT *\nFROM `sys_user`\nWHERE `status` = 1\nLIMIT 100;",
            'explanation' => '根据您的描述生成的基础查询语句，可根据实际需求调整',
            'tables' => ['sys_user'],
        ];
    }

    /**
     * 获取支持的表结构
     */
    public function getTables(): array
    {
        return [
            [
                'name' => 'sys_user',
                'comment' => '用户表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => '用户ID'],
                    ['name' => 'username', 'type' => 'varchar', 'comment' => '用户名'],
                    ['name' => 'nickname', 'type' => 'varchar', 'comment' => '昵称'],
                    ['name' => 'email', 'type' => 'varchar', 'comment' => '邮箱'],
                    ['name' => 'mobile', 'type' => 'varchar', 'comment' => '手机号'],
                    ['name' => 'status', 'type' => 'tinyint', 'comment' => '状态（1启用0禁用）'],
                    ['name' => 'create_time', 'type' => 'datetime', 'comment' => '创建时间'],
                ],
            ],
            [
                'name' => 'sys_role',
                'comment' => '角色表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => '角色ID'],
                    ['name' => 'name', 'type' => 'varchar', 'comment' => '角色名'],
                    ['name' => 'code', 'type' => 'varchar', 'comment' => '角色代码'],
                    ['name' => 'status', 'type' => 'tinyint', 'comment' => '状态'],
                    ['name' => 'create_time', 'type' => 'datetime', 'comment' => '创建时间'],
                ],
            ],
            [
                'name' => 'pay_order',
                'comment' => '订单表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => '订单ID'],
                    ['name' => 'order_no', 'type' => 'varchar', 'comment' => '订单号'],
                    ['name' => 'user_id', 'type' => 'int', 'comment' => '用户ID'],
                    ['name' => 'amount', 'type' => 'decimal', 'comment' => '金额'],
                    ['name' => 'status', 'type' => 'tinyint', 'comment' => '状态'],
                    ['name' => 'create_time', 'type' => 'datetime', 'comment' => '创建时间'],
                ],
            ],
        ];
    }
}
