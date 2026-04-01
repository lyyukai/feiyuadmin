# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: system.spec.js >> 系统管理 - 岗位管理 >> 【编辑】【删除】按钮可见
- Location: tests/system.spec.js:274:3

# Error details

```
TimeoutError: page.waitForURL: Timeout 10000ms exceeded.
=========================== logs ===========================
waiting for navigation to "**/index**" until "load"
  navigated to "http://39.105.173.6:8088/admin/login"
  navigated to "http://39.105.173.6:8088/admin/dashboard"
============================================================
```

# Page snapshot

```yaml
- generic [ref=e3]:
  - complementary [ref=e4]:
    - generic [ref=e5]:
      - img [ref=e7]
      - generic [ref=e11]: 飞羽后台
    - menubar [ref=e15]:
      - menuitem "系统管理" [expanded] [ref=e16]:
        - generic [ref=e17] [cursor=pointer]:
          - img [ref=e19]
          - generic [ref=e21]: 系统管理
          - img [ref=e23]
        - menu [ref=e25]:
          - menuitem "用户管理" [ref=e26] [cursor=pointer]:
            - img [ref=e28]
            - generic [ref=e30]: 用户管理
          - menuitem "角色管理" [ref=e31] [cursor=pointer]:
            - img [ref=e33]
            - generic [ref=e35]: 角色管理
          - menuitem "菜单管理" [ref=e36] [cursor=pointer]:
            - img [ref=e38]
            - generic [ref=e40]: 菜单管理
          - menuitem "部门管理" [ref=e41] [cursor=pointer]:
            - img [ref=e43]
            - generic [ref=e47]: 部门管理
          - menuitem "岗位管理" [ref=e48] [cursor=pointer]:
            - img [ref=e50]
            - generic [ref=e52]: 岗位管理
      - menuitem "系统配置" [ref=e53]:
        - generic [ref=e54] [cursor=pointer]:
          - img [ref=e56]
          - generic [ref=e58]: 系统配置
          - img [ref=e60]
      - menuitem "日志管理" [ref=e62]:
        - generic [ref=e63] [cursor=pointer]:
          - img [ref=e65]
          - generic [ref=e67]: 日志管理
          - img [ref=e69]
      - menuitem "扩展功能" [ref=e71]:
        - generic [ref=e72] [cursor=pointer]:
          - img [ref=e74]
          - generic [ref=e76]: 扩展功能
          - img [ref=e78]
      - menuitem "渠道管理" [ref=e80]:
        - generic [ref=e81] [cursor=pointer]:
          - img [ref=e83]
          - generic [ref=e86]: 渠道管理
          - img [ref=e88]
      - menuitem "支付管理" [ref=e90]:
        - generic [ref=e91] [cursor=pointer]:
          - img [ref=e93]
          - generic [ref=e96]: 支付管理
          - img [ref=e98]
      - menuitem "系统工具" [ref=e100]:
        - generic [ref=e101] [cursor=pointer]:
          - img [ref=e103]
          - generic [ref=e105]: 系统工具
          - img [ref=e107]
      - menuitem "工作流" [ref=e109]:
        - generic [ref=e110] [cursor=pointer]:
          - img [ref=e112]
          - generic [ref=e115]: 工作流
          - img [ref=e117]
  - generic [ref=e119]:
    - banner [ref=e120]:
      - generic [ref=e121]:
        - button [ref=e122] [cursor=pointer]:
          - img [ref=e124]
        - generic [ref=e126]: 系统管理/岗位管理
      - generic [ref=e129]:
        - img [ref=e132]
        - textbox "搜索菜单..." [ref=e134]
      - generic [ref=e135]:
        - generic "消息通知" [ref=e136] [cursor=pointer]:
          - img [ref=e139]
        - generic "全屏" [ref=e143] [cursor=pointer]:
          - img [ref=e145]
        - generic "布局配置" [ref=e147] [cursor=pointer]:
          - img [ref=e149]
        - button [ref=e152]:
          - img [ref=e153]
        - button "管理员 管理员" [ref=e156]:
          - generic [ref=e157]: 管理员
          - text: 管理员
          - img [ref=e159]
    - generic [ref=e162]:
      - generic [ref=e164] [cursor=pointer]: 工作台
      - generic [ref=e165] [cursor=pointer]:
        - generic [ref=e166]: 岗位管理
        - img [ref=e168]
    - main [ref=e170]:
      - generic [ref=e171]:
        - generic [ref=e172]:
          - button "新增" [ref=e173] [cursor=pointer]:
            - generic [ref=e174]:
              - img [ref=e176]
              - text: 新增
          - generic [ref=e178]:
            - generic [ref=e179]:
              - generic [ref=e180]: 岗位名称
              - textbox "请输入" [ref=e183]
            - button "搜索" [ref=e184] [cursor=pointer]:
              - generic [ref=e185]:
                - img [ref=e187]
                - text: 搜索
            - button "重置" [ref=e189] [cursor=pointer]:
              - generic [ref=e190]: 重置
        - generic [ref=e191]:
          - generic [ref=e193]:
            - table [ref=e195]:
              - rowgroup [ref=e204]:
                - row "序号 岗位编码 岗位名称 排序 状态 创建时间 操作" [ref=e205]:
                  - columnheader "序号" [ref=e206]:
                    - generic [ref=e207]: 序号
                  - columnheader "岗位编码" [ref=e208]:
                    - generic [ref=e209]: 岗位编码
                  - columnheader "岗位名称" [ref=e210]:
                    - generic [ref=e211]: 岗位名称
                  - columnheader "排序" [ref=e212]:
                    - generic [ref=e213]: 排序
                  - columnheader "状态" [ref=e214]:
                    - generic [ref=e215]: 状态
                  - columnheader "创建时间" [ref=e216]:
                    - generic [ref=e217]: 创建时间
                  - columnheader "操作" [ref=e218]:
                    - generic [ref=e219]: 操作
            - table [ref=e224]:
              - rowgroup [ref=e233]:
                - row "1 ceo 总经理 1 编辑 删除" [ref=e234]:
                  - cell "1" [ref=e235]:
                    - generic [ref=e236]: "1"
                  - cell "ceo" [ref=e237]:
                    - generic [ref=e238]: ceo
                  - cell "总经理" [ref=e239]:
                    - generic [ref=e240]: 总经理
                  - cell "1" [ref=e241]:
                    - generic [ref=e242]: "1"
                  - cell [ref=e243]:
                    - generic [ref=e245]:
                      - switch [checked]
                  - cell [ref=e248]
                  - cell "编辑 删除" [ref=e249]:
                    - generic [ref=e250]:
                      - button "编辑" [ref=e251] [cursor=pointer]:
                        - generic [ref=e252]: 编辑
                      - button "删除" [ref=e253] [cursor=pointer]:
                        - generic [ref=e254]: 删除
                - row "2 developer 开发工程师 2 编辑 删除" [ref=e255]:
                  - cell "2" [ref=e256]:
                    - generic [ref=e257]: "2"
                  - cell "developer" [ref=e258]:
                    - generic [ref=e259]: developer
                  - cell "开发工程师" [ref=e260]:
                    - generic [ref=e261]: 开发工程师
                  - cell "2" [ref=e262]:
                    - generic [ref=e263]: "2"
                  - cell [ref=e264]:
                    - generic [ref=e266]:
                      - switch [checked]
                  - cell [ref=e269]
                  - cell "编辑 删除" [ref=e270]:
                    - generic [ref=e271]:
                      - button "编辑" [ref=e272] [cursor=pointer]:
                        - generic [ref=e273]: 编辑
                      - button "删除" [ref=e274] [cursor=pointer]:
                        - generic [ref=e275]: 删除
                - row "3 pm 产品经理 3 编辑 删除" [ref=e276]:
                  - cell "3" [ref=e277]:
                    - generic [ref=e278]: "3"
                  - cell "pm" [ref=e279]:
                    - generic [ref=e280]: pm
                  - cell "产品经理" [ref=e281]:
                    - generic [ref=e282]: 产品经理
                  - cell "3" [ref=e283]:
                    - generic [ref=e284]: "3"
                  - cell [ref=e285]:
                    - generic [ref=e287]:
                      - switch [checked]
                  - cell [ref=e290]
                  - cell "编辑 删除" [ref=e291]:
                    - generic [ref=e292]:
                      - button "编辑" [ref=e293] [cursor=pointer]:
                        - generic [ref=e294]: 编辑
                      - button "删除" [ref=e295] [cursor=pointer]:
                        - generic [ref=e296]: 删除
          - generic [ref=e298]:
            - generic [ref=e299]: Total 3
            - generic [ref=e302] [cursor=pointer]:
              - generic:
                - combobox [ref=e304]
                - generic [ref=e305]: 10/page
              - img [ref=e308]
            - button "Go to previous page" [disabled] [ref=e310]:
              - generic:
                - img
            - list [ref=e311]:
              - listitem "page 1" [ref=e312]: "1"
            - button "Go to next page" [disabled] [ref=e313]:
              - generic:
                - img
            - generic [ref=e314]:
              - generic [ref=e315]: Go to
              - spinbutton "Page" [ref=e318]: "1"
```

# Test source

```ts
  1   | import { test, expect } from '@playwright/test';
  2   | 
  3   | const BASE = 'http://39.105.173.6:8088/admin';
  4   | 
  5   | // 先登录并跳转
  6   | test.beforeEach(async ({ page }) => {
  7   |   await page.goto(`${BASE}/login`);
  8   |   await page.waitForLoadState('networkidle');
  9   |   await page.locator('.el-input__inner').first().fill('admin');
  10  |   await page.locator('.el-input__inner').nth(1).fill('admin123');
  11  |   await page.locator('.login-btn').click();
> 12  |   await page.waitForURL('**/index**', { timeout: 10000 });
      |              ^ TimeoutError: page.waitForURL: Timeout 10000ms exceeded.
  13  | });
  14  | 
  15  | test.describe('布局与侧边栏', () => {
  16  |   test('首页加载完整，侧边栏可见', async ({ page }) => {
  17  |     await expect(page.locator('.sidebar')).toBeVisible();
  18  |     await expect(page.locator('.sidebar-logo')).toBeVisible();
  19  |     await expect(page.locator('.sidebar-logo .logo-text')).toContainText('飞羽后台');
  20  |   });
  21  | 
  22  |   test('折叠按钮存在，可切换侧边栏状态', async ({ page }) => {
  23  |     const collapseBtn = page.locator('.collapse-btn');
  24  |     await expect(collapseBtn).toBeVisible();
  25  | 
  26  |     // 折叠
  27  |     await collapseBtn.click();
  28  |     await expect(page.locator('.sidebar')).toHaveClass(/collapsed/);
  29  | 
  30  |     // 展开
  31  |     await collapseBtn.click();
  32  |     await expect(page.locator('.sidebar')).not.toHaveClass(/collapsed/);
  33  |   });
  34  | 
  35  |   test('顶部头部可见，显示面包屑路径', async ({ page }) => {
  36  |     await expect(page.locator('.header')).toBeVisible();
  37  |     await expect(page.locator('.current-path')).toBeVisible();
  38  |   });
  39  | 
  40  |   test('右侧用户菜单可点击', async ({ page }) => {
  41  |     const userMenu = page.locator('.user-dropdown, .user-info, [class*="user"]').first();
  42  |     await expect(userMenu).toBeVisible();
  43  |   });
  44  | });
  45  | 
  46  | test.describe('系统管理 - 用户管理', () => {
  47  |   test.beforeEach(async ({ page }) => {
  48  |     await page.goto(`${BASE}/system/user`);
  49  |     await page.waitForLoadState('networkidle');
  50  |     await page.waitForTimeout(1000);
  51  |   });
  52  | 
  53  |   // ===== 顶部按钮 =====
  54  |   test('【新增用户】按钮可见可点击，弹出表单对话框', async ({ page }) => {
  55  |     const addBtn = page.locator('.toolbar button').filter({ hasText: '新增' }).first();
  56  |     await expect(addBtn).toBeVisible();
  57  | 
  58  |     await addBtn.click();
  59  |     const dialog = page.locator('.el-dialog');
  60  |     await expect(dialog).toBeVisible();
  61  |     await expect(dialog.locator('.el-dialog__header').filter({ hasText: '新增' })).toBeVisible();
  62  |   });
  63  | 
  64  |   test('【批量删除】按钮存在（选中多条记录后应可用）', async ({ page }) => {
  65  |     const batchDeleteBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '批量删除' });
  66  |     await expect(batchDeleteBtn.first()).toBeVisible();
  67  |   });
  68  | 
  69  |   test('【导出】按钮可见可点击', async ({ page }) => {
  70  |     const exportBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '导出' });
  71  |     await expect(exportBtn.first()).toBeVisible();
  72  |     await exportBtn.first().click();
  73  |   });
  74  | 
  75  |   test('【刷新】按钮可见可点击', async ({ page }) => {
  76  |     const refreshBtn = page.locator('button').filter({ hasText: '刷新' });
  77  |     await expect(refreshBtn.first()).toBeVisible();
  78  |   });
  79  | 
  80  |   // ===== 搜索栏按钮 =====
  81  |   test('【搜索】按钮可点击，数据重新加载', async ({ page }) => {
  82  |     const searchBtn = page.locator('button').filter({ hasText: '搜索' });
  83  |     await expect(searchBtn).toBeVisible();
  84  |     await searchBtn.click();
  85  |   });
  86  | 
  87  |   test('【重置】按钮可点击，清空搜索表单', async ({ page }) => {
  88  |     const resetBtn = page.locator('button').filter({ hasText: '重置' });
  89  |     await expect(resetBtn).toBeVisible();
  90  |     await resetBtn.click();
  91  |   });
  92  | 
  93  |   test('状态筛选下拉框可用', async ({ page }) => {
  94  |     const select = page.locator('.el-select').first();
  95  |     await expect(select).toBeVisible();
  96  |     await select.click();
  97  |     await expect(page.locator('.el-select-dropdown')).toBeVisible();
  98  |   });
  99  | 
  100 |   // ===== 表格列按钮 =====
  101 |   test('【编辑】按钮可见可点击，弹出编辑对话框', async ({ page }) => {
  102 |     // 等待表格加载
  103 |     await page.waitForSelector('.el-table__row', { timeout: 5000 });
  104 |     const editBtn = page.locator('.el-table__row .el-button').filter({ hasText: '编辑' }).first();
  105 |     await expect(editBtn).toBeVisible();
  106 |     await editBtn.click();
  107 | 
  108 |     const dialog = page.locator('.el-dialog');
  109 |     await expect(dialog).toBeVisible();
  110 |     await expect(dialog.locator('.el-dialog__header').filter({ hasText: '编辑' })).toBeVisible();
  111 |   });
  112 | 
```