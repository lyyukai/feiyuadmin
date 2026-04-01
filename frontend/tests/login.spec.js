import { test, expect } from '@playwright/test';

const BASE = 'http://39.105.173.6:8088/admin';

test.describe('登录页面', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/login`);
    await page.waitForLoadState('networkidle');
  });

  test('页面标题和核心元素完整', async ({ page }) => {
    await expect(page.locator('.brand-name')).toContainText('飞羽后台管理系统');
    await expect(page.locator('.login-header h2')).toContainText('欢迎回来');
    await expect(page.locator('.login-btn')).toBeVisible();
  });

  test('用户名输入框可用', async ({ page }) => {
    const input = page.locator('.el-input__inner').first();
    await expect(input).toBeVisible();
    await input.fill('admin');
    await expect(input).toHaveValue('admin');
  });

  test('密码输入框可用', async ({ page }) => {
    const inputs = page.locator('.el-input__inner');
    const pwdInput = inputs.nth(1);
    await expect(pwdInput).toBeVisible();
    await pwdInput.fill('admin123');
    await expect(pwdInput).toHaveValue('admin123');
  });

  test('记住密码复选框可用', async ({ page }) => {
    const checkbox = page.locator('.el-checkbox');
    await expect(checkbox).toBeVisible();
    await checkbox.click();
    await expect(checkbox.locator('.el-checkbox__input')).toHaveClass(/is-checked/);
  });

  test('登录按钮默认状态可点击', async ({ page }) => {
    const btn = page.locator('.login-btn');
    await expect(btn).toBeEnabled();
    await expect(btn).not.toHaveClass(/is-loading/);
  });

  test('空表单点击登录 — 显示校验提示', async ({ page }) => {
    await page.locator('.el-input__inner').first().fill('');
    await page.locator('.el-input__inner').nth(1).fill('');
    await page.locator('.login-btn').click();
    await expect(page.locator('.el-form-item__error').first()).toBeVisible();
  });

  test('密码不足6位 — 显示校验提示', async ({ page }) => {
    await page.locator('.el-input__inner').first().fill('admin');
    await page.locator('.el-input__inner').nth(1).fill('123');
    await page.locator('.login-btn').click();
    await expect(page.locator('.el-form-item__error')).toContainText('密码长度不能少于6位');
  });

  test('正确凭据登录成功，跳转首页', async ({ page }) => {
    await page.locator('.el-input__inner').first().fill('admin');
    await page.locator('.el-input__inner').nth(1).fill('admin123');
    await page.locator('.login-btn').click();
    // 等待跳转
    await page.waitForURL('**/index**', { timeout: 10000 });
    await expect(page.url()).toContain('/index');
  });

  test('错误密码登录失败，显示错误提示', async ({ page }) => {
    await page.locator('.el-input__inner').first().fill('admin');
    await page.locator('.el-input__inner').nth(1).fill('wrongpassword');
    await page.locator('.login-btn').click();
    await expect(page.locator('.el-message')).toBeVisible({ timeout: 5000 });
  });
});
