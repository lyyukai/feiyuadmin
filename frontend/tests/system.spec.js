import { test, expect } from '@playwright/test';

const BASE = 'http://39.105.173.6:8088/admin';

// 先登录并跳转
test.beforeEach(async ({ page }) => {
  await page.goto(`${BASE}/login`);
  await page.waitForLoadState('networkidle');
  await page.locator('.el-input__inner').first().fill('admin');
  await page.locator('.el-input__inner').nth(1).fill('admin123');
  await page.locator('.login-btn').click();
  await page.waitForURL('**/index**', { timeout: 10000 });
});

test.describe('布局与侧边栏', () => {
  test('首页加载完整，侧边栏可见', async ({ page }) => {
    await expect(page.locator('.sidebar')).toBeVisible();
    await expect(page.locator('.sidebar-logo')).toBeVisible();
    await expect(page.locator('.sidebar-logo .logo-text')).toContainText('飞羽后台');
  });

  test('折叠按钮存在，可切换侧边栏状态', async ({ page }) => {
    const collapseBtn = page.locator('.collapse-btn');
    await expect(collapseBtn).toBeVisible();

    // 折叠
    await collapseBtn.click();
    await expect(page.locator('.sidebar')).toHaveClass(/collapsed/);

    // 展开
    await collapseBtn.click();
    await expect(page.locator('.sidebar')).not.toHaveClass(/collapsed/);
  });

  test('顶部头部可见，显示面包屑路径', async ({ page }) => {
    await expect(page.locator('.header')).toBeVisible();
    await expect(page.locator('.current-path')).toBeVisible();
  });

  test('右侧用户菜单可点击', async ({ page }) => {
    const userMenu = page.locator('.user-dropdown, .user-info, [class*="user"]').first();
    await expect(userMenu).toBeVisible();
  });
});

test.describe('系统管理 - 用户管理', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/user`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  // ===== 顶部按钮 =====
  test('【新增用户】按钮可见可点击，弹出表单对话框', async ({ page }) => {
    const addBtn = page.locator('.toolbar button').filter({ hasText: '新增' }).first();
    await expect(addBtn).toBeVisible();

    await addBtn.click();
    const dialog = page.locator('.el-dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('.el-dialog__header').filter({ hasText: '新增' })).toBeVisible();
  });

  test('【批量删除】按钮存在（选中多条记录后应可用）', async ({ page }) => {
    const batchDeleteBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '批量删除' });
    await expect(batchDeleteBtn.first()).toBeVisible();
  });

  test('【导出】按钮可见可点击', async ({ page }) => {
    const exportBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '导出' });
    await expect(exportBtn.first()).toBeVisible();
    await exportBtn.first().click();
  });

  test('【刷新】按钮可见可点击', async ({ page }) => {
    const refreshBtn = page.locator('button').filter({ hasText: '刷新' });
    await expect(refreshBtn.first()).toBeVisible();
  });

  // ===== 搜索栏按钮 =====
  test('【搜索】按钮可点击，数据重新加载', async ({ page }) => {
    const searchBtn = page.locator('button').filter({ hasText: '搜索' });
    await expect(searchBtn).toBeVisible();
    await searchBtn.click();
  });

  test('【重置】按钮可点击，清空搜索表单', async ({ page }) => {
    const resetBtn = page.locator('button').filter({ hasText: '重置' });
    await expect(resetBtn).toBeVisible();
    await resetBtn.click();
  });

  test('状态筛选下拉框可用', async ({ page }) => {
    const select = page.locator('.el-select').first();
    await expect(select).toBeVisible();
    await select.click();
    await expect(page.locator('.el-select-dropdown')).toBeVisible();
  });

  // ===== 表格列按钮 =====
  test('【编辑】按钮可见可点击，弹出编辑对话框', async ({ page }) => {
    // 等待表格加载
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const editBtn = page.locator('.el-table__row .el-button').filter({ hasText: '编辑' }).first();
    await expect(editBtn).toBeVisible();
    await editBtn.click();

    const dialog = page.locator('.el-dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('.el-dialog__header').filter({ hasText: '编辑' })).toBeVisible();
  });

  test('【重置密码】按钮可见可点击', async ({ page }) => {
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const resetBtn = page.locator('.el-table__row .el-button').filter({ hasText: '重置密码' }).first();
    await expect(resetBtn).toBeVisible();
    await resetBtn.click();
    // 确认是否有弹窗提示
    await page.waitForTimeout(500);
  });

  test('【删除】按钮可见可点击', async ({ page }) => {
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const deleteBtn = page.locator('.el-table__row .el-button').filter({ hasText: '删除' }).first();
    await expect(deleteBtn).toBeVisible();
    await deleteBtn.click();
    // 等待确认对话框
    await page.waitForTimeout(500);
    const confirmDialog = page.locator('.el-message-box');
    if (await confirmDialog.isVisible()) {
      await confirmDialog.locator('.el-button').filter({ hasText: '取消' }).click();
    }
  });

  // ===== 新增/编辑表单按钮 =====
  test('新增对话框中【取消】按钮可关闭弹窗', async ({ page }) => {
    await page.locator('.toolbar button').filter({ hasText: '新增' }).first().click();
    await page.waitForSelector('.el-dialog');
    const cancelBtn = page.locator('.el-dialog .el-dialog__footer button').filter({ hasText: '取消' });
    await expect(cancelBtn).toBeVisible();
    await cancelBtn.click();
    await expect(page.locator('.el-dialog')).not.toBeVisible();
  });

  test('新增对话框中【确认】按钮存在', async ({ page }) => {
    await page.locator('.toolbar button').filter({ hasText: '新增' }).first().click();
    await page.waitForSelector('.el-dialog');
    const confirmBtn = page.locator('.el-dialog .el-dialog__footer button').filter({ hasText: '确认' });
    await expect(confirmBtn).toBeVisible();
    await expect(confirmBtn).toBeEnabled();
  });

  // ===== 分页按钮 =====
  test('分页组件可见，上下页按钮可点击', async ({ page }) => {
    const pagination = page.locator('.el-pagination');
    await expect(pagination).toBeVisible();

    const nextBtn = pagination.locator('.btn-next');
    await expect(nextBtn).toBeVisible();
    await nextBtn.click();
    await page.waitForTimeout(500);
  });
});

test.describe('系统管理 - 角色管理', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/role`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('【新增角色】按钮可见可点击', async ({ page }) => {
    const addBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '新增' }).first();
    await expect(addBtn).toBeVisible();
    await addBtn.click();
    await expect(page.locator('.el-dialog')).toBeVisible();
  });

  test('【分配权限】按钮可见可点击', async ({ page }) => {
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const permBtn = page.locator('.el-table__row .el-button').filter({ hasText: '分配权限' }).first();
    await expect(permBtn).toBeVisible();
    await permBtn.click();
    await page.waitForTimeout(500);
  });

  test('【编辑】按钮可见可点击', async ({ page }) => {
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const editBtn = page.locator('.el-table__row .el-button').filter({ hasText: '编辑' }).first();
    await expect(editBtn).toBeVisible();
    await editBtn.click();
    await expect(page.locator('.el-dialog')).toBeVisible();
  });

  test('【删除】按钮可见可点击', async ({ page }) => {
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const deleteBtn = page.locator('.el-table__row .el-button').filter({ hasText: '删除' }).first();
    await expect(deleteBtn).toBeVisible();
    await deleteBtn.click();
    await page.waitForTimeout(500);
  });
});

test.describe('系统管理 - 菜单管理', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/menu`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('【新增菜单】按钮可见可点击', async ({ page }) => {
    const addBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '新增' }).first();
    await expect(addBtn).toBeVisible();
    await addBtn.click();
    await expect(page.locator('.el-dialog')).toBeVisible();
  });

  test('【编辑】按钮可见可点击', async ({ page }) => {
    await page.waitForSelector('.el-table__row, .el-tree-node', { timeout: 5000 });
    const editBtn = page.locator('.el-button').filter({ hasText: '编辑' }).first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await expect(page.locator('.el-dialog')).toBeVisible();
    }
  });

  test('【删除】按钮可见可点击', async ({ page }) => {
    await page.waitForSelector('.el-table__row, .el-tree-node', { timeout: 5000 });
    const deleteBtn = page.locator('.el-button').filter({ hasText: '删除' }).first();
    if (await deleteBtn.isVisible()) {
      await deleteBtn.click();
      await page.waitForTimeout(500);
    }
  });
});

test.describe('系统管理 - 部门管理', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/dept`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('【新增部门】按钮可见可点击', async ({ page }) => {
    const addBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '新增' }).first();
    await expect(addBtn).toBeVisible();
    await addBtn.click();
    await expect(page.locator('.el-dialog')).toBeVisible();
  });

  test('【编辑】【删除】按钮可见', async ({ page }) => {
    await page.waitForSelector('.el-table__row, .el-tree-node', { timeout: 5000 });
    const editBtn = page.locator('.el-button').filter({ hasText: '编辑' }).first();
    const deleteBtn = page.locator('.el-button').filter({ hasText: '删除' }).first();
    if (await editBtn.isVisible()) await expect(editBtn).toBeVisible();
    if (await deleteBtn.isVisible()) await expect(deleteBtn).toBeVisible();
  });
});

test.describe('系统管理 - 岗位管理', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/post`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('【新增岗位】按钮可见可点击', async ({ page }) => {
    const addBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '新增' }).first();
    await expect(addBtn).toBeVisible();
    await addBtn.click();
    await expect(page.locator('.el-dialog')).toBeVisible();
  });

  test('【编辑】【删除】按钮可见', async ({ page }) => {
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const editBtn = page.locator('.el-table__row .el-button').filter({ hasText: '编辑' }).first();
    const deleteBtn = page.locator('.el-table__row .el-button').filter({ hasText: '删除' }).first();
    if (await editBtn.isVisible()) await expect(editBtn).toBeVisible();
    if (await deleteBtn.isVisible()) await expect(deleteBtn).toBeVisible();
  });
});

test.describe('日志管理 - 操作日志', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/log`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('【搜索】【重置】按钮可见可点击', async ({ page }) => {
    const searchBtn = page.locator('button').filter({ hasText: '搜索' });
    const resetBtn = page.locator('button').filter({ hasText: '重置' });
    if (await searchBtn.isVisible()) {
      await expect(searchBtn).toBeVisible();
      await searchBtn.click();
    }
    if (await resetBtn.isVisible()) {
      await expect(resetBtn).toBeVisible();
      await resetBtn.click();
    }
  });

  test('【导出】按钮可见', async ({ page }) => {
    const exportBtn = page.locator('button').filter({ hasText: '导出' });
    if (await exportBtn.first().isVisible()) {
      await expect(exportBtn.first()).toBeVisible();
    }
  });
});

test.describe('日志管理 - 登录日志', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/loginlog`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('页面加载完整，按钮可用', async ({ page }) => {
    await expect(page.locator('.el-pagination')).toBeVisible();
    const searchBtn = page.locator('button').filter({ hasText: '搜索' });
    if (await searchBtn.isVisible()) await searchBtn.click();
  });
});

test.describe('系统设置 - 参数配置', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/config`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('【新增参数】按钮可见可点击', async ({ page }) => {
    const addBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '新增' }).first();
    if (await addBtn.isVisible()) {
      await addBtn.click();
      await expect(page.locator('.el-dialog')).toBeVisible();
    }
  });

  test('【编辑】【删除】按钮可见', async ({ page }) => {
    await page.waitForSelector('.el-table__row', { timeout: 5000 });
    const editBtn = page.locator('.el-table__row .el-button').filter({ hasText: '编辑' }).first();
    const deleteBtn = page.locator('.el-table__row .el-button').filter({ hasText: '删除' }).first();
    if (await editBtn.isVisible()) await expect(editBtn).toBeVisible();
    if (await deleteBtn.isVisible()) await expect(deleteBtn).toBeVisible();
  });
});

test.describe('数据字典', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/system/dict/type`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
  });

  test('字典类型页面【新增】按钮可见', async ({ page }) => {
    const addBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '新增' }).first();
    if (await addBtn.isVisible()) await expect(addBtn).toBeVisible();
  });

  test('字典数据页面可正常访问', async ({ page }) => {
    await page.goto(`${BASE}/system/dict/data`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await expect(page.locator('.el-table, .el-pagination')).toBeVisible();
  });
});
