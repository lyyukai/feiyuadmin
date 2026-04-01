<?php
namespace app\database\migrations;

use think\db\exception\PDOException;
use think\migrate\driver\mysql\Blueprint;

return new class
{
    public function up(): void
    {
        echo "Running migration: create_member_table\n";
        
        try {
            // 会员表 - 与管理员表分开
            $sql = "CREATE TABLE IF NOT EXISTS `member` (
              `id`          INT(11)      NOT NULL AUTO_INCREMENT COMMENT '会员ID',
              `username`    VARCHAR(50)  NOT NULL                  COMMENT '会员账号',
              `nickname`    VARCHAR(50)  DEFAULT NULL              COMMENT '会员昵称',
              `password`    VARCHAR(255) NOT NULL                  COMMENT '密码（加密存储）',
              `avatar`      VARCHAR(255) DEFAULT NULL              COMMENT '头像URL',
              `mobile`      VARCHAR(20)  DEFAULT NULL               COMMENT '手机号',
              `email`       VARCHAR(100) DEFAULT NULL               COMMENT '邮箱',
              `level_id`    INT(11)      DEFAULT NULL               COMMENT '会员等级ID',
              `status`      TINYINT(1)   DEFAULT 1                  COMMENT '状态（0停用1正常）',
              `last_login_ip` VARCHAR(50) DEFAULT NULL              COMMENT '最后登录IP',
              `last_login_time` DATETIME DEFAULT NULL               COMMENT '最后登录时间',
              `create_by`   INT(11)      DEFAULT NULL               COMMENT '创建者ID',
              `create_time` DATETIME     DEFAULT NULL               COMMENT '创建时间',
              `update_time` DATETIME     DEFAULT NULL                COMMENT '更新时间',
              PRIMARY KEY (`id`),
              UNIQUE KEY `uk_username` (`username`),
              KEY `idx_level_id` (`level_id`),
              KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员表';";
            
            echo "SQL: $sql\n";
            echo "Migration completed: create_member_table\n";
        } catch (PDOException $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    public function down(): void
    {
        echo "Rolling back migration: create_member_table\n";
    }
};
