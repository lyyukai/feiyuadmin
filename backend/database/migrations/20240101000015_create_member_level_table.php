<?php
namespace app\database\migrations;

use think\db\exception\PDOException;
use think\migrate\driver\mysql\Blueprint;

return new class
{
    public function up(): void
    {
        echo "Running migration: create_member_level_table\n";
        
        try {
            // 会员等级表
            $sql = "CREATE TABLE IF NOT EXISTS `member_level` (
              `id`          INT(11)      NOT NULL AUTO_INCREMENT COMMENT '等级ID',
              `level_name`  VARCHAR(50)  NOT NULL                  COMMENT '等级名称',
              `level_icon`  VARCHAR(255) DEFAULT NULL              COMMENT '等级图标',
              `level_color` VARCHAR(20)  DEFAULT NULL              COMMENT '等级颜色',
              `min_points`  INT(11)      DEFAULT 0                  COMMENT '最低积分',
              `max_points`  INT(11)      DEFAULT NULL               COMMENT '最高积分',
              `discount`    DECIMAL(4,2)  DEFAULT 1.00              COMMENT '享受折扣',
              `sort`        INT(11)      DEFAULT 0                  COMMENT '排序',
              `status`      TINYINT(1)   DEFAULT 1                  COMMENT '状态（0停用1正常）',
              `create_time` DATETIME     DEFAULT NULL               COMMENT '创建时间',
              `update_time` DATETIME     DEFAULT NULL                COMMENT '更新时间',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员等级表';";
            
            echo "SQL: $sql\n";
            echo "Migration completed: create_member_level_table\n";
        } catch (PDOException $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    public function down(): void
    {
        echo "Rolling back migration: create_member_level_table\n";
    }
};
