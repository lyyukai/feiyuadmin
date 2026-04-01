# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: system.spec.js >> 布局与侧边栏 >> 首页加载完整，侧边栏可见
- Location: tests/system.spec.js:16:3

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
      - menuitem "系统管理" [ref=e16]:
        - generic [ref=e17] [cursor=pointer]:
          - img [ref=e19]
          - generic [ref=e21]: 系统管理
          - img [ref=e23]
      - menuitem "系统配置" [ref=e25]:
        - generic [ref=e26] [cursor=pointer]:
          - img [ref=e28]
          - generic [ref=e30]: 系统配置
          - img [ref=e32]
      - menuitem "日志管理" [ref=e34]:
        - generic [ref=e35] [cursor=pointer]:
          - img [ref=e37]
          - generic [ref=e39]: 日志管理
          - img [ref=e41]
      - menuitem "扩展功能" [ref=e43]:
        - generic [ref=e44] [cursor=pointer]:
          - img [ref=e46]
          - generic [ref=e48]: 扩展功能
          - img [ref=e50]
      - menuitem "渠道管理" [ref=e52]:
        - generic [ref=e53] [cursor=pointer]:
          - img [ref=e55]
          - generic [ref=e58]: 渠道管理
          - img [ref=e60]
      - menuitem "支付管理" [ref=e62]:
        - generic [ref=e63] [cursor=pointer]:
          - img [ref=e65]
          - generic [ref=e68]: 支付管理
          - img [ref=e70]
      - menuitem "系统工具" [ref=e72]:
        - generic [ref=e73] [cursor=pointer]:
          - img [ref=e75]
          - generic [ref=e77]: 系统工具
          - img [ref=e79]
      - menuitem "工作流" [ref=e81]:
        - generic [ref=e82] [cursor=pointer]:
          - img [ref=e84]
          - generic [ref=e87]: 工作流
          - img [ref=e89]
  - generic [ref=e91]:
    - banner [ref=e92]:
      - generic [ref=e93]:
        - button [ref=e94] [cursor=pointer]:
          - img [ref=e96]
        - generic [ref=e98]: 工作台
      - generic [ref=e101]:
        - img [ref=e104]
        - textbox "搜索菜单..." [ref=e106]
      - generic [ref=e107]:
        - generic "消息通知" [ref=e108] [cursor=pointer]:
          - img [ref=e111]
        - generic "全屏" [ref=e115] [cursor=pointer]:
          - img [ref=e117]
        - generic "布局配置" [ref=e119] [cursor=pointer]:
          - img [ref=e121]
        - button [ref=e124]:
          - img [ref=e125]
        - button "管理员 管理员" [ref=e128]:
          - generic [ref=e129]: 管理员
          - text: 管理员
          - img [ref=e131]
    - generic [ref=e136] [cursor=pointer]: 工作台
    - main [ref=e137]:
      - generic [ref=e138]:
        - generic [ref=e139]:
          - generic [ref=e141]:
            - img [ref=e144]
            - generic [ref=e146]:
              - generic [ref=e147]: "4"
              - generic [ref=e148]: 用户总数
          - generic [ref=e150]:
            - img [ref=e153]
            - generic [ref=e155]:
              - generic [ref=e156]: "3"
              - generic [ref=e157]: 角色数量
          - generic [ref=e159]:
            - img [ref=e162]
            - generic [ref=e164]:
              - generic [ref=e165]: "128"
              - generic [ref=e166]: 今日访问
          - generic [ref=e168]:
            - img [ref=e171]
            - generic [ref=e173]:
              - generic [ref=e174]: "56"
              - generic [ref=e175]: 操作日志
        - generic [ref=e176]:
          - generic [ref=e178]:
            - generic [ref=e180]: 快捷入口
            - generic [ref=e182]:
              - generic [ref=e183] [cursor=pointer]:
                - img [ref=e185]
                - generic [ref=e187]: 用户管理
              - generic [ref=e188] [cursor=pointer]:
                - img [ref=e190]
                - generic [ref=e192]: 角色管理
              - generic [ref=e193] [cursor=pointer]:
                - img [ref=e195]
                - generic [ref=e197]: 菜单管理
              - generic [ref=e198] [cursor=pointer]:
                - img [ref=e200]
                - generic [ref=e204]: 部门管理
              - generic [ref=e205] [cursor=pointer]:
                - img [ref=e207]
                - generic [ref=e209]: 岗位管理
              - generic [ref=e210] [cursor=pointer]:
                - img [ref=e212]
                - generic [ref=e214]: 系统配置
          - generic [ref=e216]:
            - generic [ref=e217]: 最新用户
            - generic [ref=e220]:
              - table [ref=e222]:
                - rowgroup [ref=e227]:
                  - row "用户名 昵称 最后登录" [ref=e228]:
                    - columnheader "用户名" [ref=e229]:
                      - generic [ref=e230]: 用户名
                    - columnheader "昵称" [ref=e231]:
                      - generic [ref=e232]: 昵称
                    - columnheader "最后登录" [ref=e233]:
                      - generic [ref=e234]: 最后登录
              - table [ref=e239]:
                - rowgroup [ref=e244]:
                  - row "admin 管理员 2026-03-31 18:00:00" [ref=e245]:
                    - cell "admin" [ref=e246]:
                      - generic [ref=e247]: admin
                    - cell "管理员" [ref=e248]:
                      - generic [ref=e249]: 管理员
                    - cell "2026-03-31 18:00:00" [ref=e250]:
                      - generic [ref=e251]: 2026-03-31 18:00:00
                  - row "zhangsan 张三 2026-03-31 17:30:00" [ref=e252]:
                    - cell "zhangsan" [ref=e253]:
                      - generic [ref=e254]: zhangsan
                    - cell "张三" [ref=e255]:
                      - generic [ref=e256]: 张三
                    - cell "2026-03-31 17:30:00" [ref=e257]:
                      - generic [ref=e258]: 2026-03-31 17:30:00
                  - row "lisi 李四 2026-03-31 16:45:00" [ref=e259]:
                    - cell "lisi" [ref=e260]:
                      - generic [ref=e261]: lisi
                    - cell "李四" [ref=e262]:
                      - generic [ref=e263]: 李四
                    - cell "2026-03-31 16:45:00" [ref=e264]:
                      - generic [ref=e265]: 2026-03-31 16:45:00
        - generic [ref=e268]:
          - generic [ref=e269]:
            - generic [ref=e270]: 最新操作日志
            - button "查看更多" [ref=e271] [cursor=pointer]:
              - generic [ref=e272]: 查看更多
          - generic [ref=e275]:
            - table [ref=e277]:
              - rowgroup [ref=e284]:
                - row "操作人 操作类型 操作内容 IP地址 操作时间" [ref=e285]:
                  - columnheader "操作人" [ref=e286]:
                    - generic [ref=e287]: 操作人
                  - columnheader "操作类型" [ref=e288]:
                    - generic [ref=e289]: 操作类型
                  - columnheader "操作内容" [ref=e290]:
                    - generic [ref=e291]: 操作内容
                  - columnheader "IP地址" [ref=e292]:
                    - generic [ref=e293]: IP地址
                  - columnheader "操作时间" [ref=e294]:
                    - generic [ref=e295]: 操作时间
            - table [ref=e300]:
              - rowgroup [ref=e307]:
                - row "admin 登录 用户登录系统 127.0.0.1 2026-03-31 18:00:00" [ref=e308]:
                  - cell "admin" [ref=e309]:
                    - generic [ref=e310]: admin
                  - cell "登录" [ref=e311]:
                    - generic [ref=e312]: 登录
                  - cell "用户登录系统" [ref=e313]:
                    - generic [ref=e314]: 用户登录系统
                  - cell "127.0.0.1" [ref=e315]:
                    - generic [ref=e316]: 127.0.0.1
                  - cell "2026-03-31 18:00:00" [ref=e317]:
                    - generic [ref=e318]: 2026-03-31 18:00:00
                - row "admin 编辑 修改用户信息 127.0.0.1 2026-03-31 17:55:00" [ref=e319]:
                  - cell "admin" [ref=e320]:
                    - generic [ref=e321]: admin
                  - cell "编辑" [ref=e322]:
                    - generic [ref=e323]: 编辑
                  - cell "修改用户信息" [ref=e324]:
                    - generic [ref=e325]: 修改用户信息
                  - cell "127.0.0.1" [ref=e326]:
                    - generic [ref=e327]: 127.0.0.1
                  - cell "2026-03-31 17:55:00" [ref=e328]:
                    - generic [ref=e329]: 2026-03-31 17:55:00
                - row "admin 创建 新增角色：普通员工 127.0.0.1 2026-03-31 17:30:00" [ref=e330]:
                  - cell "admin" [ref=e331]:
                    - generic [ref=e332]: admin
                  - cell "创建" [ref=e333]:
                    - generic [ref=e334]: 创建
                  - cell "新增角色：普通员工" [ref=e335]:
                    - generic [ref=e336]: 新增角色：普通员工
                  - cell "127.0.0.1" [ref=e337]:
                    - generic [ref=e338]: 127.0.0.1
                  - cell "2026-03-31 17:30:00" [ref=e339]:
                    - generic [ref=e340]: 2026-03-31 17:30:00
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