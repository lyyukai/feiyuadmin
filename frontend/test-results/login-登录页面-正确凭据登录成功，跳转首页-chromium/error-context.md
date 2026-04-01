# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: login.spec.js >> 登录页面 >> 正确凭据登录成功，跳转首页
- Location: tests/login.spec.js:59:3

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
  1  | import { test, expect } from '@playwright/test';
  2  | 
  3  | const BASE = 'http://39.105.173.6:8088/admin';
  4  | 
  5  | test.describe('登录页面', () => {
  6  |   test.beforeEach(async ({ page }) => {
  7  |     await page.goto(`${BASE}/login`);
  8  |     await page.waitForLoadState('networkidle');
  9  |   });
  10 | 
  11 |   test('页面标题和核心元素完整', async ({ page }) => {
  12 |     await expect(page.locator('.brand-name')).toContainText('飞羽后台管理系统');
  13 |     await expect(page.locator('.login-header h2')).toContainText('欢迎回来');
  14 |     await expect(page.locator('.login-btn')).toBeVisible();
  15 |   });
  16 | 
  17 |   test('用户名输入框可用', async ({ page }) => {
  18 |     const input = page.locator('.el-input__inner').first();
  19 |     await expect(input).toBeVisible();
  20 |     await input.fill('admin');
  21 |     await expect(input).toHaveValue('admin');
  22 |   });
  23 | 
  24 |   test('密码输入框可用', async ({ page }) => {
  25 |     const inputs = page.locator('.el-input__inner');
  26 |     const pwdInput = inputs.nth(1);
  27 |     await expect(pwdInput).toBeVisible();
  28 |     await pwdInput.fill('admin123');
  29 |     await expect(pwdInput).toHaveValue('admin123');
  30 |   });
  31 | 
  32 |   test('记住密码复选框可用', async ({ page }) => {
  33 |     const checkbox = page.locator('.el-checkbox');
  34 |     await expect(checkbox).toBeVisible();
  35 |     await checkbox.click();
  36 |     await expect(checkbox.locator('.el-checkbox__input')).toHaveClass(/is-checked/);
  37 |   });
  38 | 
  39 |   test('登录按钮默认状态可点击', async ({ page }) => {
  40 |     const btn = page.locator('.login-btn');
  41 |     await expect(btn).toBeEnabled();
  42 |     await expect(btn).not.toHaveClass(/is-loading/);
  43 |   });
  44 | 
  45 |   test('空表单点击登录 — 显示校验提示', async ({ page }) => {
  46 |     await page.locator('.el-input__inner').first().fill('');
  47 |     await page.locator('.el-input__inner').nth(1).fill('');
  48 |     await page.locator('.login-btn').click();
  49 |     await expect(page.locator('.el-form-item__error').first()).toBeVisible();
  50 |   });
  51 | 
  52 |   test('密码不足6位 — 显示校验提示', async ({ page }) => {
  53 |     await page.locator('.el-input__inner').first().fill('admin');
  54 |     await page.locator('.el-input__inner').nth(1).fill('123');
  55 |     await page.locator('.login-btn').click();
  56 |     await expect(page.locator('.el-form-item__error')).toContainText('密码长度不能少于6位');
  57 |   });
  58 | 
  59 |   test('正确凭据登录成功，跳转首页', async ({ page }) => {
  60 |     await page.locator('.el-input__inner').first().fill('admin');
  61 |     await page.locator('.el-input__inner').nth(1).fill('admin123');
  62 |     await page.locator('.login-btn').click();
  63 |     // 等待跳转
> 64 |     await page.waitForURL('**/index**', { timeout: 10000 });
     |                ^ TimeoutError: page.waitForURL: Timeout 10000ms exceeded.
  65 |     await expect(page.url()).toContain('/index');
  66 |   });
  67 | 
  68 |   test('错误密码登录失败，显示错误提示', async ({ page }) => {
  69 |     await page.locator('.el-input__inner').first().fill('admin');
  70 |     await page.locator('.el-input__inner').nth(1).fill('wrongpassword');
  71 |     await page.locator('.login-btn').click();
  72 |     await expect(page.locator('.el-message')).toBeVisible({ timeout: 5000 });
  73 |   });
  74 | });
  75 | 
```