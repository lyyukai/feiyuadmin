<?php
/**
 * AI Prompt模板管理控制器
 * 
 * 注意：需要先在 feiyuadmin 数据库创建 ai_prompt_template 表
 * SQL 位于：/root/feiyuadminthink/8/app/service/ai/database.sql
 */

declare(strict_types=1);

namespace app\adminapi\controller\ai;

use app\adminapi\controller\BaseAdminController;
use think\response\Json;

/**
 * Prompt模板管理
 */
class PromptController extends BaseAdminController
{
    private string $dbName = 'feiyuadmin';
    private string $table = 'ai_prompt_template';
    
    /**
     * 获取模板列表
     * GET /adminapi/ai/prompt/list
     */
    public function list(): Json
    {
        $status = $this->request->param('status', '');
        $keyword = $this->request->param('keyword', '');
        $page = (int) $this->request->param('page', 1);
        $pageSize = (int) $this->request->param('page_size', 20);
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            
            $where = "1=1";
            if ($status !== '') {
                $where .= " AND status=" . (int)$status;
            }
            if (!empty($keyword)) {
                $where .= " AND (name LIKE '%{$keyword}%' OR code LIKE '%{$keyword}%')";
            }
            
            $total = $model->query("SELECT COUNT(*) as ct FROM {$this->table} WHERE {$where}")[0]['ct'] ?? 0;
            $offset = ($page - 1) * $pageSize;
            $list = $model->query("SELECT * FROM {$this->table} WHERE {$where} ORDER BY id DESC LIMIT {$offset},{$pageSize}");
            
            return $this->success('获取成功', ['list' => $list, 'total' => $total, 'page' => $page, 'page_size' => $pageSize]);
        } catch (\Exception $e) {
            return $this->fail('获取失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 获取单个模板
     * GET /adminapi/ai/prompt/detail
     */
    public function detail(): Json
    {
        $id = (int) $this->request->param('id', 0);
        
        if (!$id) {
            return $this->fail('模板ID不能为空');
        }
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            $template = $model->query("SELECT * FROM {$this->table} WHERE id=?", [$id]);
            
            if (empty($template)) {
                return $this->fail('模板不存在');
            }
            
            return $this->success('获取成功', $template[0]);
        } catch (\Exception $e) {
            return $this->fail('获取失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 创建模板
     * POST /adminapi/ai/prompt/save
     */
    public function save(): Json
    {
        $data = [
            'name' => $this->request->param('name', ''),
            'code' => $this->request->param('code', ''),
            'description' => $this->request->param('description', ''),
            'system_prompt' => $this->request->param('system_prompt', ''),
            'user_prompt_prefix' => $this->request->param('user_prompt_prefix', ''),
            'user_prompt_suffix' => $this->request->param('user_prompt_suffix', ''),
            'variables' => json_encode($this->request->param('variables', [])),
            'model' => $this->request->param('model', 'deepseek-ai/DeepSeek-V3'),
            'temperature' => (float) $this->request->param('temperature', 0.7),
            'max_tokens' => (int) $this->request->param('max_tokens', 4096),
            'status' => (int) $this->request->param('status', 1),
            'is_default' => (int) $this->request->param('is_default', 0),
            'version' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        if (empty($data['name'])) {
            return $this->fail('模板名称不能为空');
        }
        if (empty($data['code'])) {
            $data['code'] = 'tmpl_' . uniqid();
        }
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            $model->table($this->table)->insert($data);
            $id = $model->query("SELECT LAST_INSERT_ID()")[0]['LAST_INSERT_ID()'];
            
            // 创建初始版本
            $model->query(
                "INSERT INTO ai_prompt_template_version (template_id, version, content, created_at) VALUES (?, 1, ?, NOW())",
                [$id, $data['system_prompt']]
            );
            
            return $this->success('创建成功', ['id' => $id]);
        } catch (\Exception $e) {
            return $this->fail('创建失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 更新模板
     * POST /adminapi/ai/prompt/update
     */
    public function update(): Json
    {
        $id = (int) $this->request->param('id', 0);
        
        if (!$id) {
            return $this->fail('模板ID不能为空');
        }
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            $existing = $model->query("SELECT * FROM {$this->table} WHERE id=?", [$id]);
            if (empty($existing)) {
                return $this->fail('模板不存在');
            }
            
            $updateFields = ['name', 'code', 'description', 'system_prompt', 'user_prompt_prefix', 
                              'user_prompt_suffix', 'variables', 'model', 'status', 'is_default'];
            $floatFields = ['temperature'];
            $intFields = ['max_tokens'];
            
            $data = ['updated_at' => date('Y-m-d H:i:s')];
            
            foreach ($updateFields as $field) {
                if ($this->request->has($field)) {
                    $data[$field] = $this->request->param($field);
                }
            }
            foreach ($floatFields as $field) {
                if ($this->request->has($field)) {
                    $data[$field] = (float) $this->request->param($field);
                }
            }
            foreach ($intFields as $field) {
                if ($this->request->has($field)) {
                    $data[$field] = (int) $this->request->param($field);
                }
            }
            
            $changeNote = $this->request->param('change_note', '');
            $oldVersion = (int) $existing[0]['version'];
            $newVersion = $oldVersion + 1;
            
            // 更新模板版本号
            $data['version'] = $newVersion;
            
            $setParts = [];
            $values = [];
            foreach ($data as $k => $v) {
                $setParts[] = "{$k}=?";
                $values[] = $v;
            }
            $values[] = $id;
            
            $model->execute("UPDATE {$this->table} SET " . implode(',', $setParts) . " WHERE id=?", $values);
            
            // 记录版本历史
            if (!empty($data['system_prompt'])) {
                $model->execute(
                    "INSERT INTO ai_prompt_template_version (template_id, version, content, change_note, created_at) VALUES (?, ?, ?, ?, NOW())",
                    [$id, $newVersion, $data['system_prompt'], $changeNote]
                );
            }
            
            return $this->success('更新成功');
        } catch (\Exception $e) {
            return $this->fail('更新失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 删除模板
     * POST /adminapi/ai/prompt/delete
     */
    public function delete(): Json
    {
        $id = (int) $this->request->param('id', 0);
        
        if (!$id) {
            return $this->fail('模板ID不能为空');
        }
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            $model->execute("DELETE FROM {$this->table} WHERE id=?", [$id]);
            $model->execute("DELETE FROM ai_prompt_template_version WHERE template_id=?", [$id]);
            
            return $this->success('删除成功');
        } catch (\Exception $e) {
            return $this->fail('删除失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 复制模板
     * POST /adminapi/ai/prompt/clone
     */
    public function clone(): Json
    {
        $id = (int) $this->request->param('id', 0);
        $newName = $this->request->param('new_name', '');
        
        if (!$id) {
            return $this->fail('模板ID不能为空');
        }
        if (empty($newName)) {
            return $this->fail('新模板名称不能为空');
        }
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            $original = $model->query("SELECT * FROM {$this->table} WHERE id=?", [$id]);
            if (empty($original)) {
                return $this->fail('模板不存在');
            }
            
            $orig = $original[0];
            $newCode = $orig['code'] . '_copy_' . time();
            
            $data = [
                'name' => $newName,
                'code' => $newCode,
                'description' => $orig['description'],
                'system_prompt' => $orig['system_prompt'],
                'user_prompt_prefix' => $orig['user_prompt_prefix'],
                'user_prompt_suffix' => $orig['user_prompt_suffix'],
                'variables' => $orig['variables'],
                'model' => $orig['model'],
                'temperature' => $orig['temperature'],
                'max_tokens' => $orig['max_tokens'],
                'status' => 1,
                'is_default' => 0,
                'version' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $model->table($this->table)->insert($data);
            $newId = $model->query("SELECT LAST_INSERT_ID()")[0]['LAST_INSERT_ID()'];
            
            $model->execute(
                "INSERT INTO ai_prompt_template_version (template_id, version, content, created_at) VALUES (?, 1, ?, NOW())",
                [$newId, $orig['system_prompt']]
            );
            
            return $this->success('复制成功', ['id' => $newId]);
        } catch (\Exception $e) {
            return $this->fail('复制失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 获取版本历史
     * GET /adminapi/ai/prompt/versions
     */
    public function versions(): Json
    {
        $templateId = (int) $this->request->param('id', 0);
        
        if (!$templateId) {
            return $this->fail('模板ID不能为空');
        }
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            $versions = $model->query(
                "SELECT * FROM ai_prompt_template_version WHERE template_id=? ORDER BY version DESC",
                [$templateId]
            );
            
            return $this->success('获取成功', $versions);
        } catch (\Exception $e) {
            return $this->fail('获取失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 回滚模板
     * POST /adminapi/ai/prompt/rollback
     */
    public function rollback(): Json
    {
        $templateId = (int) $this->request->param('id', 0);
        $version = (int) $this->request->param('version', 0);
        
        if (!$templateId || !$version) {
            return $this->fail('参数不完整');
        }
        
        try {
            $model = \think\facade\Db::connect($this->dbName);
            $versionRecord = $model->query(
                "SELECT * FROM ai_prompt_template_version WHERE template_id=? AND version=?",
                [$templateId, $version]
            );
            
            if (empty($versionRecord)) {
                return $this->fail('版本不存在');
            }
            
            $currentVersion = $model->query("SELECT version FROM {$this->table} WHERE id=?", [$templateId]);
            $newVersion = ((int) $currentVersion[0]['version']) + 1;
            
            $model->execute(
                "UPDATE {$this->table} SET system_prompt=?, version=?, updated_at=NOW() WHERE id=?",
                [$versionRecord[0]['content'], $newVersion, $templateId]
            );
            
            $model->execute(
                "INSERT INTO ai_prompt_template_version (template_id, version, content, change_note, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$templateId, $newVersion, $versionRecord[0]['content'], "回滚到版本 {$version}"]
            );
            
            return $this->success('回滚成功');
        } catch (\Exception $e) {
            return $this->fail('回滚失败: ' . $e->getMessage());
        }
    }
}
