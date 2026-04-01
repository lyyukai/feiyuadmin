# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: login.spec.js >> 登录页面 >> 错误密码登录失败，显示错误提示
- Location: tests/login.spec.js:68:3

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('.el-message')
Expected: visible
Error: strict mode violation: locator('.el-message') resolved to 2 elements:
    1) <div role="alert" id="message_1" class="el-message el-message--error is-center el-message-fade-enter-active el-message-fade-enter-to">…</div> aka locator('#message_1')
    2) <div role="alert" id="message_2" class="el-message el-message--error is-center el-message-fade-enter-active el-message-fade-enter-to">…</div> aka locator('#message_2')

Call log:
  - Expect "toBeVisible" with timeout 5000ms
  - waiting for locator('.el-message')

```

# Page snapshot

```yaml
- generic [active] [ref=e1]:
  - generic [ref=e3]:
    - generic [ref=e4]:
      - generic [ref=e5]:
        - img [ref=e8]
        - heading "飞羽后台管理系统" [level=1] [ref=e13]
        - paragraph [ref=e14]: 安全 · 高效 · 稳定
      - generic [ref=e15]:
        - generic [ref=e16]:
          - img [ref=e19]
          - generic [ref=e21]:
            - heading "安全可靠" [level=4] [ref=e22]
            - paragraph [ref=e23]: 多重防护，数据安全
        - generic [ref=e24]:
          - img [ref=e27]
          - generic [ref=e30]:
            - heading "高效处理" [level=4] [ref=e31]
            - paragraph [ref=e32]: 秒级响应，流畅体验
        - generic [ref=e33]:
          - img [ref=e36]
          - generic [ref=e38]:
            - heading "数据可视化" [level=4] [ref=e39]
            - paragraph [ref=e40]: 数据驱动，决策明智
    - generic [ref=e42]:
      - generic [ref=e43]:
        - heading "欢迎回来" [level=2] [ref=e44]
        - paragraph [ref=e45]: 请登录您的账号继续使用
      - generic [ref=e46]:
        - generic [ref=e49]:
          - img [ref=e51]
          - textbox "请输入用户名 / 手机号" [ref=e55]: admin
        - generic [ref=e58]:
          - img [ref=e60]
          - generic [ref=e64]:
            - textbox "请输入密码" [ref=e65]: wrongpassword
            - img [ref=e68] [cursor=pointer]
        - generic [ref=e71]:
          - generic [ref=e72] [cursor=pointer]:
            - generic [ref=e73]:
              - checkbox "记住密码"
            - generic [ref=e75]: 记住密码
          - generic [ref=e77] [cursor=pointer]: 忘记密码？
        - button "登 录" [ref=e78] [cursor=pointer]:
          - generic [ref=e80]: 登 录
      - paragraph [ref=e82]: 默认账号：admin / admin123
  - alert [ref=e83]:
    - img [ref=e85]
    - paragraph [ref=e87]: 用户名或密码错误
  - alert [ref=e88]:
    - img [ref=e90]
    - paragraph [ref=e92]: 用户名或密码错误
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
  64 |     await page.waitForURL('**/index**', { timeout: 10000 });
  65 |     await expect(page.url()).toContain('/index');
  66 |   });
  67 | 
  68 |   test('错误密码登录失败，显示错误提示', async ({ page }) => {
  69 |     await page.locator('.el-input__inner').first().fill('admin');
  70 |     await page.locator('.el-input__inner').nth(1).fill('wrongpassword');
  71 |     await page.locator('.login-btn').click();
> 72 |     await expect(page.locator('.el-message')).toBeVisible({ timeout: 5000 });
     |                                               ^ Error: expect(locator).toBeVisible() failed
  73 |   });
  74 | });
  75 | 
```