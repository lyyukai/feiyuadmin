<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 消息模板模型
 */
class NoticeTemplate extends Model
{
    protected $name = 'notice_template';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * 状态文本
     */
    public function getStatusTextAttr($value, $data): string
    {
        return ($data['status'] ?? 1) == self::STATUS_ENABLED ? '启用' : '禁用';
    }

    /**
     * 获取变量列表
     */
    public function getVarsAttr($value): array
    {
        return $value ? array_filter(array_map('trim', explode(',', $value))) : [];
    }

    /**
     * 设置变量列表
     */
    public function setVarsAttr($value): string
    {
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value ?? '';
    }

    /**
     * 变量替换
     */
    public function render(array $vars): array
    {
        $title = $this->title ?? '';
        $content = $this->content ?? '';

        foreach ($this->vars as $var) {
            $replace = $vars[$var] ?? '';
            $title = str_replace('${' . $var . '}', (string) $replace, $title);
            $title = str_replace('{{' . $var . '}}', (string) $replace, $title);
            $content = str_replace('${' . $var . '}', (string) $replace, $content);
            $content = str_replace('{{' . $var . '}}', (string) $replace, $content);
        }

        return [
            'title' => $title,
            'content' => $content,
        ];
    }

    /**
     * 按编码获取模板
     */
    public static function getByCode(string $code): ?self
    {
        return self::where('code', $code)->where('status', self::STATUS_ENABLED)->find();
    }
}
