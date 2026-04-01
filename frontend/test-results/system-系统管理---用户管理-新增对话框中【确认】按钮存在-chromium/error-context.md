# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: system.spec.js >> 系统管理 - 用户管理 >> 新增对话框中【确认】按钮存在
- Location: tests/system.spec.js:145:3

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
        - generic [ref=e126]: 系统管理/用户管理
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
        - generic [ref=e166]: 用户管理
        - img [ref=e168]
    - main [ref=e170]:
      - generic [ref=e171]:
        - generic [ref=e172]:
          - button "新增" [ref=e173] [cursor=pointer]:
            - generic [ref=e174]:
              - img [ref=e176]
              - text: 新增
          - button "批量操作" [ref=e179] [cursor=pointer]:
            - generic [ref=e180]:
              - text: 批量操作
              - img [ref=e182]
          - generic [ref=e184]:
            - generic [ref=e185]:
              - generic [ref=e186]: 关键词
              - textbox "用户名/昵称" [ref=e189]
            - generic [ref=e190]:
              - generic [ref=e191]: 状态
              - generic [ref=e193] [cursor=pointer]:
                - generic:
                  - combobox [ref=e195]
                  - generic [ref=e196]: 请选择
                - img [ref=e199]
            - button "搜索" [ref=e201] [cursor=pointer]:
              - generic [ref=e202]:
                - img [ref=e204]
                - text: 搜索
            - button "重置" [ref=e206] [cursor=pointer]:
              - generic [ref=e207]: 重置
        - generic [ref=e208]:
          - generic [ref=e210]: 用户列表
          - generic [ref=e212]:
            - table [ref=e214]:
              - rowgroup [ref=e225]:
                - row "Select all rows 序号 用户名 昵称 邮箱 手机号 状态 最后登录 操作" [ref=e226]:
                  - columnheader "Select all rows" [ref=e227]:
                    - generic "Select all rows" [ref=e229] [cursor=pointer]:
                      - generic [ref=e230]:
                        - checkbox "Select all rows"
                  - columnheader "序号" [ref=e232]:
                    - generic [ref=e233]: 序号
                  - columnheader "用户名" [ref=e234]:
                    - generic [ref=e235]: 用户名
                  - columnheader "昵称" [ref=e236]:
                    - generic [ref=e237]: 昵称
                  - columnheader "邮箱" [ref=e238]:
                    - generic [ref=e239]: 邮箱
                  - columnheader "手机号" [ref=e240]:
                    - generic [ref=e241]: 手机号
                  - columnheader "状态" [ref=e242]:
                    - generic [ref=e243]: 状态
                  - columnheader "最后登录" [ref=e244]:
                    - generic [ref=e245]: 最后登录
                  - columnheader "操作" [ref=e246]:
                    - generic [ref=e247]: 操作
            - table [ref=e252]:
              - rowgroup [ref=e263]:
                - row "Select this row 7 testuser 测试用户 正常 编辑 重置密码 删除" [ref=e264]:
                  - cell "Select this row" [ref=e265]:
                    - generic "Select this row" [ref=e267] [cursor=pointer]:
                      - generic [ref=e268]:
                        - checkbox "Select this row"
                  - cell "7" [ref=e270]:
                    - generic [ref=e271]: "7"
                  - cell "testuser" [ref=e272]:
                    - generic [ref=e273]: testuser
                  - cell "测试用户" [ref=e274]:
                    - generic [ref=e275]: 测试用户
                  - cell [ref=e276]
                  - cell [ref=e277]
                  - cell "正常" [ref=e278]:
                    - generic [ref=e280]: 正常
                  - cell [ref=e281]
                  - cell "编辑 重置密码 删除" [ref=e282]:
                    - generic [ref=e283]:
                      - button "编辑" [ref=e284] [cursor=pointer]:
                        - generic [ref=e285]: 编辑
                      - button "重置密码" [ref=e286] [cursor=pointer]:
                        - generic [ref=e287]: 重置密码
                      - button "删除" [ref=e288] [cursor=pointer]:
                        - generic [ref=e289]: 删除
                - row "Select this row 6 test001 测试用户 正常 编辑 重置密码 删除" [ref=e290]:
                  - cell "Select this row" [ref=e291]:
                    - generic "Select this row" [ref=e293] [cursor=pointer]:
                      - generic [ref=e294]:
                        - checkbox "Select this row"
                  - cell "6" [ref=e296]:
                    - generic [ref=e297]: "6"
                  - cell "test001" [ref=e298]:
                    - generic [ref=e299]: test001
                  - cell "测试用户" [ref=e300]:
                    - generic [ref=e301]: 测试用户
                  - cell [ref=e302]
                  - cell [ref=e303]
                  - cell "正常" [ref=e304]:
                    - generic [ref=e306]: 正常
                  - cell [ref=e307]
                  - cell "编辑 重置密码 删除" [ref=e308]:
                    - generic [ref=e309]:
                      - button "编辑" [ref=e310] [cursor=pointer]:
                        - generic [ref=e311]: 编辑
                      - button "重置密码" [ref=e312] [cursor=pointer]:
                        - generic [ref=e313]: 重置密码
                      - button "删除" [ref=e314] [cursor=pointer]:
                        - generic [ref=e315]: 删除
                - row "Select this row 5 sadsadsad 123 正常 编辑 重置密码 删除" [ref=e316]:
                  - cell "Select this row" [ref=e317]:
                    - generic "Select this row" [ref=e319] [cursor=pointer]:
                      - generic [ref=e320]:
                        - checkbox "Select this row"
                  - cell "5" [ref=e322]:
                    - generic [ref=e323]: "5"
                  - cell "sadsadsad" [ref=e324]:
                    - generic [ref=e325]: sadsadsad
                  - cell "123" [ref=e326]:
                    - generic [ref=e327]: "123"
                  - cell [ref=e328]
                  - cell [ref=e329]
                  - cell "正常" [ref=e330]:
                    - generic [ref=e332]: 正常
                  - cell [ref=e333]
                  - cell "编辑 重置密码 删除" [ref=e334]:
                    - generic [ref=e335]:
                      - button "编辑" [ref=e336] [cursor=pointer]:
                        - generic [ref=e337]: 编辑
                      - button "重置密码" [ref=e338] [cursor=pointer]:
                        - generic [ref=e339]: 重置密码
                      - button "删除" [ref=e340] [cursor=pointer]:
                        - generic [ref=e341]: 删除
                - row "Select this row 4 lisi 李四 正常 编辑 重置密码 删除" [ref=e342]:
                  - cell "Select this row" [ref=e343]:
                    - generic "Select this row" [ref=e345] [cursor=pointer]:
                      - generic [ref=e346]:
                        - checkbox "Select this row"
                  - cell "4" [ref=e348]:
                    - generic [ref=e349]: "4"
                  - cell "lisi" [ref=e350]:
                    - generic [ref=e351]: lisi
                  - cell "李四" [ref=e352]:
                    - generic [ref=e353]: 李四
                  - cell [ref=e354]
                  - cell [ref=e355]
                  - cell "正常" [ref=e356]:
                    - generic [ref=e358]: 正常
                  - cell [ref=e359]
                  - cell "编辑 重置密码 删除" [ref=e360]:
                    - generic [ref=e361]:
                      - button "编辑" [ref=e362] [cursor=pointer]:
                        - generic [ref=e363]: 编辑
                      - button "重置密码" [ref=e364] [cursor=pointer]:
                        - generic [ref=e365]: 重置密码
                      - button "删除" [ref=e366] [cursor=pointer]:
                        - generic [ref=e367]: 删除
                - row "Select this row 3 zhangsan 张三 正常 编辑 重置密码 删除" [ref=e368]:
                  - cell "Select this row" [ref=e369]:
                    - generic "Select this row" [ref=e371] [cursor=pointer]:
                      - generic [ref=e372]:
                        - checkbox "Select this row"
                  - cell "3" [ref=e374]:
                    - generic [ref=e375]: "3"
                  - cell "zhangsan" [ref=e376]:
                    - generic [ref=e377]: zhangsan
                  - cell "张三" [ref=e378]:
                    - generic [ref=e379]: 张三
                  - cell [ref=e380]
                  - cell [ref=e381]
                  - cell "正常" [ref=e382]:
                    - generic [ref=e384]: 正常
                  - cell [ref=e385]
                  - cell "编辑 重置密码 删除" [ref=e386]:
                    - generic [ref=e387]:
                      - button "编辑" [ref=e388] [cursor=pointer]:
                        - generic [ref=e389]: 编辑
                      - button "重置密码" [ref=e390] [cursor=pointer]:
                        - generic [ref=e391]: 重置密码
                      - button "删除" [ref=e392] [cursor=pointer]:
                        - generic [ref=e393]: 删除
                - row "Select this row 2 test 测试用户-已编辑 禁用 编辑 重置密码 删除" [ref=e394]:
                  - cell "Select this row" [ref=e395]:
                    - generic "Select this row" [ref=e397] [cursor=pointer]:
                      - generic [ref=e398]:
                        - checkbox "Select this row"
                  - cell "2" [ref=e400]:
                    - generic [ref=e401]: "2"
                  - cell "test" [ref=e402]:
                    - generic [ref=e403]: test
                  - cell "测试用户-已编辑" [ref=e404]:
                    - generic [ref=e405]: 测试用户-已编辑
                  - cell [ref=e406]
                  - cell [ref=e407]
                  - cell "禁用" [ref=e408]:
                    - generic [ref=e410]: 禁用
                  - cell [ref=e411]
                  - cell "编辑 重置密码 删除" [ref=e412]:
                    - generic [ref=e413]:
                      - button "编辑" [ref=e414] [cursor=pointer]:
                        - generic [ref=e415]: 编辑
                      - button "重置密码" [ref=e416] [cursor=pointer]:
                        - generic [ref=e417]: 重置密码
                      - button "删除" [ref=e418] [cursor=pointer]:
                        - generic [ref=e419]: 删除
                - row "Select this row 1 admin 张三-已修改 正常 2026-04-02 00:43:46 编辑 重置密码 删除" [ref=e420]:
                  - cell "Select this row" [ref=e421]:
                    - generic "Select this row" [ref=e423] [cursor=pointer]:
                      - generic [ref=e424]:
                        - checkbox "Select this row"
                  - cell "1" [ref=e426]:
                    - generic [ref=e427]: "1"
                  - cell "admin" [ref=e428]:
                    - generic [ref=e429]: admin
                  - cell "张三-已修改" [ref=e430]:
                    - generic [ref=e431]: 张三-已修改
                  - cell [ref=e432]
                  - cell [ref=e433]
                  - cell "正常" [ref=e434]:
                    - generic [ref=e436]: 正常
                  - cell "2026-04-02 00:43:46" [ref=e437]:
                    - generic [ref=e438]: 2026-04-02 00:43:46
                  - cell "编辑 重置密码 删除" [ref=e439]:
                    - generic [ref=e440]:
                      - button "编辑" [ref=e441] [cursor=pointer]:
                        - generic [ref=e442]: 编辑
                      - button "重置密码" [ref=e443] [cursor=pointer]:
                        - generic [ref=e444]: 重置密码
                      - button "删除" [ref=e445] [cursor=pointer]:
                        - generic [ref=e446]: 删除
          - generic [ref=e448]:
            - generic [ref=e449]: Total 7
            - generic [ref=e452] [cursor=pointer]:
              - generic:
                - combobox [ref=e454]
                - generic [ref=e455]: 20/page
              - img [ref=e458]
            - button "Go to previous page" [disabled] [ref=e460]:
              - generic:
                - img
            - list [ref=e461]:
              - listitem "page 1" [ref=e462]: "1"
            - button "Go to next page" [disabled] [ref=e463]:
              - generic:
                - img
            - generic [ref=e464]:
              - generic [ref=e465]: Go to
              - spinbutton "Page" [ref=e468]: "1"
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