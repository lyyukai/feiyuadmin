# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: system.spec.js >> 系统管理 - 角色管理 >> 【新增角色】按钮可见可点击
- Location: tests/system.spec.js:172:3

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
        - generic [ref=e126]: 系统管理/角色管理
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
        - generic [ref=e166]: 角色管理
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
              - generic [ref=e180]: 角色名称
              - textbox "请输入" [ref=e183]
            - button "搜索" [ref=e184] [cursor=pointer]:
              - generic [ref=e185]:
                - img [ref=e187]
                - text: 搜索
            - button "重置" [ref=e189] [cursor=pointer]:
              - generic [ref=e190]: 重置
        - generic [ref=e191]:
          - generic [ref=e193]: 角色列表
          - generic [ref=e195]:
            - table [ref=e197]:
              - rowgroup [ref=e205]:
                - row "序号 角色名称 角色编码 备注 状态 操作" [ref=e206]:
                  - columnheader "序号" [ref=e207]:
                    - generic [ref=e208]: 序号
                  - columnheader "角色名称" [ref=e209]:
                    - generic [ref=e210]: 角色名称
                  - columnheader "角色编码" [ref=e211]:
                    - generic [ref=e212]: 角色编码
                  - columnheader "备注" [ref=e213]:
                    - generic [ref=e214]: 备注
                  - columnheader "状态" [ref=e215]:
                    - generic [ref=e216]: 状态
                  - columnheader "操作" [ref=e217]:
                    - generic [ref=e218]: 操作
            - table [ref=e223]:
              - rowgroup [ref=e231]:
                - row "1 超级管理员 super_admin 正常 编辑 权限 删除" [ref=e232]:
                  - cell "1" [ref=e233]:
                    - generic [ref=e234]: "1"
                  - cell "超级管理员" [ref=e235]:
                    - generic [ref=e236]: 超级管理员
                  - cell "super_admin" [ref=e237]:
                    - generic [ref=e238]: super_admin
                  - cell [ref=e239]
                  - cell "正常" [ref=e240]:
                    - generic [ref=e242]: 正常
                  - cell "编辑 权限 删除" [ref=e243]:
                    - generic [ref=e244]:
                      - button "编辑" [ref=e245] [cursor=pointer]:
                        - generic [ref=e246]: 编辑
                      - button "权限" [ref=e247] [cursor=pointer]:
                        - generic [ref=e248]: 权限
                      - button "删除" [ref=e249] [cursor=pointer]:
                        - generic [ref=e250]: 删除
                - row "2 运营主管 operator 正常 编辑 权限 删除" [ref=e251]:
                  - cell "2" [ref=e252]:
                    - generic [ref=e253]: "2"
                  - cell "运营主管" [ref=e254]:
                    - generic [ref=e255]: 运营主管
                  - cell "operator" [ref=e256]:
                    - generic [ref=e257]: operator
                  - cell [ref=e258]
                  - cell "正常" [ref=e259]:
                    - generic [ref=e261]: 正常
                  - cell "编辑 权限 删除" [ref=e262]:
                    - generic [ref=e263]:
                      - button "编辑" [ref=e264] [cursor=pointer]:
                        - generic [ref=e265]: 编辑
                      - button "权限" [ref=e266] [cursor=pointer]:
                        - generic [ref=e267]: 权限
                      - button "删除" [ref=e268] [cursor=pointer]:
                        - generic [ref=e269]: 删除
                - row "3 普通员工 staff 正常 编辑 权限 删除" [ref=e270]:
                  - cell "3" [ref=e271]:
                    - generic [ref=e272]: "3"
                  - cell "普通员工" [ref=e273]:
                    - generic [ref=e274]: 普通员工
                  - cell "staff" [ref=e275]:
                    - generic [ref=e276]: staff
                  - cell [ref=e277]
                  - cell "正常" [ref=e278]:
                    - generic [ref=e280]: 正常
                  - cell "编辑 权限 删除" [ref=e281]:
                    - generic [ref=e282]:
                      - button "编辑" [ref=e283] [cursor=pointer]:
                        - generic [ref=e284]: 编辑
                      - button "权限" [ref=e285] [cursor=pointer]:
                        - generic [ref=e286]: 权限
                      - button "删除" [ref=e287] [cursor=pointer]:
                        - generic [ref=e288]: 删除
          - generic [ref=e290]:
            - generic [ref=e291]: Total 3
            - generic [ref=e294] [cursor=pointer]:
              - generic:
                - combobox [ref=e296]
                - generic [ref=e297]: 20/page
              - img [ref=e300]
            - button "Go to previous page" [disabled] [ref=e302]:
              - generic:
                - img
            - list [ref=e303]:
              - listitem "page 1" [ref=e304]: "1"
            - button "Go to next page" [disabled] [ref=e305]:
              - generic:
                - img
            - generic [ref=e306]:
              - generic [ref=e307]: Go to
              - spinbutton "Page" [ref=e310]: "1"
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