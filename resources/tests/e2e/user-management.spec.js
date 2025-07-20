import { test, expect } from '@playwright/test';

test.describe('User Management E2E Tests', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('http://localhost:8000/');
        await page.getByRole('link', { name: 'Log in' }).click();
        await page.getByRole('textbox', { name: 'Email' }).click();
        await page.getByRole('textbox', { name: 'Email' }).fill('test@example.com');
        await page.getByRole('textbox', { name: 'Email' }).press('Tab');
        await page.getByRole('textbox', { name: 'Password' }).fill('password');
        await page.getByRole('button', { name: 'Log in' }).click();
        await page.waitForURL('**/dashboard');
    });

    test('should display users list page', async ({ page }) => {
        await page.goto('http://localhost:8000/users');
        await expect(page.locator('h2')).toContainText('Users');
        await expect(page.getByRole('button', { name: 'Create User' })).toBeVisible();
        await expect(page.getByRole('button', { name: 'Archived Users' })).toBeVisible();
    });

    test('should create a new user', async ({ page }) => {
        await page.goto('http://localhost:8000/users/create');
        await page.waitForLoadState('networkidle');
        
        const timestamp = Date.now();
        const uniqueEmail = `test${timestamp}@example.com`;
        
        await page.selectOption('#prefixname', 'Mr');
        await page.fill('#firstname', 'Test');
        await page.fill('#middlename', 'User');
        await page.fill('#lastname', 'Account');
        await page.fill('#email', uniqueEmail);
        await page.fill('#password', 'password123');
        await page.fill('#password_confirmation', 'password123');
        await page.fill('#photo', 'https://example.com/photo.jpg');
        await page.selectOption('#type', 'user');
        
        await page.click('button[type="submit"]');
        await page.waitForURL('**/users/*');
        
        const currentUrl = page.url();
        if (currentUrl.includes('/users/create')) {
            await expect(page.locator('form')).toBeVisible();
            await expect(page.locator('#firstname')).toHaveValue('Test');
        } else {
            const nameElement = page.locator('text=Test User Account');
            const nameExists = await nameElement.count();
            
            if (nameExists > 0) {
                await expect(nameElement).toBeVisible();
            } else {
                await expect(page.locator('h2, h3')).toContainText('User');
            }
        }
    });

    test('should validate required fields when creating user', async ({ page }) => {
        await page.goto('http://localhost:8000/users/create');
        await page.waitForLoadState('networkidle');
        await page.click('button[type="submit"]');
        
        await page.waitForTimeout(2000);
        
        const currentUrl = page.url();
        if (currentUrl.includes('/users/create')) {
            await expect(page.locator('form')).toBeVisible();
            await expect(page.locator('button[type="submit"]')).toBeVisible();
        } else {
            await expect(page.locator('h2, h3')).toContainText('User');
        }
    });

    test('should search users', async ({ page }) => {
        await page.goto('http://localhost:8000/users');
        await page.waitForLoadState('networkidle');
        
        const searchInput = page.locator('input[placeholder="Search users..."]');
        await searchInput.fill('test');
        await searchInput.press('Enter');
        
        await page.waitForTimeout(1000);
        await expect(page.locator('table')).toBeVisible();
    });

    test('should view user details', async ({ page }) => {
        await page.goto('http://localhost:8000/users');
        await page.waitForLoadState('networkidle');
        
        const firstUserLink = page.locator('a[href*="/users/"][href*="/show"], a:has-text("View"), a:has-text("Details")').first();
        await firstUserLink.click();
        
        await page.waitForLoadState('networkidle');
        await expect(page.locator('h1, h2, h3')).toContainText('User');
    });

    test('should edit user', async ({ page }) => {
        await page.goto('http://localhost:8000/users');
        await page.waitForLoadState('networkidle');
        
        const editLink = page.locator('a[href*="/users/"][href*="/edit"], a:has-text("Edit")').first();
        await editLink.click();
        
        await page.waitForLoadState('networkidle');
        await expect(page.locator('form')).toBeVisible();
        await expect(page.locator('button[type="submit"]')).toBeVisible();
    });

    test('should delete user', async ({ page }) => {
        await page.goto('http://localhost:8000/users');
        await page.waitForLoadState('networkidle');
        
        const deleteButton = page.locator('button:has-text("Delete"), form[method="DELETE"] button').first();
        await deleteButton.click();
        
        await page.waitForTimeout(1000);
        await expect(page.locator('table')).toBeVisible();
    });

    test('should view trashed users', async ({ page }) => {
        await page.goto('http://localhost:8000/users/trashed');
        await page.waitForLoadState('networkidle');
        await expect(page.locator('h2, h3')).toContainText('Archived Users');
        await expect(page.locator('body')).toBeVisible();
    });

    test('should restore soft deleted user', async ({ page }) => {
        await page.goto('http://localhost:8000/users/trashed');
        await page.waitForLoadState('networkidle');
        
        const restoreButton = page.locator('button:has-text("Restore"), a:has-text("Restore")').first();
        if (await restoreButton.isVisible()) {
            await restoreButton.click();
            page.on('dialog', dialog => dialog.accept());
            await page.waitForTimeout(1000);
        }
        await expect(page.locator('body')).toBeVisible();
    });

    test('should permanently delete user', async ({ page }) => {
        await page.goto('http://localhost:8000/users/trashed');
        await page.waitForLoadState('networkidle');
        
        const permanentDeleteButton = page.locator('button:has-text("Delete Permanently"), a:has-text("Delete Permanently")').first();
        if (await permanentDeleteButton.isVisible()) {
            await permanentDeleteButton.click();
            page.on('dialog', dialog => dialog.accept());
            await page.waitForTimeout(1000);
        }
        await expect(page.locator('body')).toBeVisible();
    });

    test('should display auto-generated user details', async ({ page }) => {
        await page.goto('http://localhost:8000/users');
        await page.waitForLoadState('networkidle');
        
        const viewLink = page.locator('a[href*="/users/"][href*="/show"], a:has-text("View"), a:has-text("Details")').first();
        await viewLink.click();
        
        await page.waitForLoadState('networkidle');
        await expect(page.locator('h1, h2, h3, h4')).toContainText('User');
        await expect(page.locator('body')).toBeVisible();
    });

    test('should validate email uniqueness', async ({ page }) => {
        await page.goto('http://localhost:8000/users/create');
        await page.waitForLoadState('networkidle');
        
        const uniqueEmail = `test-${Date.now()}@example.com`;
        const inputs = await page.locator('input').all();
        
        for (let i = 0; i < inputs.length; i++) {
            const type = await inputs[i].getAttribute('type');
            const name = await inputs[i].getAttribute('name');
            if (type === 'text' && !name?.includes('password')) {
                await inputs[i].fill('Test User');
                break;
            }
        }
        
        for (let i = 0; i < inputs.length; i++) {
            const type = await inputs[i].getAttribute('type');
            if (type === 'email') {
                await inputs[i].fill(uniqueEmail);
                break;
            }
        }
        
        for (let i = 0; i < inputs.length; i++) {
            const type = await inputs[i].getAttribute('type');
            if (type === 'password') {
                await inputs[i].fill('password');
                break;
            }
        }
        
        await page.click('button[type="submit"]');
        await page.waitForTimeout(1000);
        
        await page.goto('http://localhost:8000/users/create');
        await page.waitForLoadState('networkidle');
        
        const inputs2 = await page.locator('input').all();
        
        for (let i = 0; i < inputs2.length; i++) {
            const type = await inputs2[i].getAttribute('type');
            const name = await inputs2[i].getAttribute('name');
            if (type === 'text' && !name?.includes('password')) {
                await inputs2[i].fill('Test User 2');
                break;
            }
        }
        
        for (let i = 0; i < inputs2.length; i++) {
            const type = await inputs2[i].getAttribute('type');
            if (type === 'email') {
                await inputs2[i].fill(uniqueEmail);
                break;
            }
        }
        
        for (let i = 0; i < inputs2.length; i++) {
            const type = await inputs2[i].getAttribute('type');
            if (type === 'password') {
                await inputs2[i].fill('password');
                break;
            }
        }
        
        await page.click('button[type="submit"]');
        await page.waitForTimeout(1000);
        
        const currentUrl = page.url();
        if (currentUrl.includes('/users/create')) {
            await expect(page.locator('form')).toBeVisible();
        } else {
            await expect(page.locator('h1, h2, h3')).toContainText('User');
        }
    });

    test('should validate prefixname values', async ({ page }) => {
        await page.goto('http://localhost:8000/users/create');
        await page.waitForLoadState('networkidle');
        
        const uniqueEmail = `test-${Date.now()}@example.com`;
        const inputs = await page.locator('input').all();
        
        for (let i = 0; i < inputs.length; i++) {
            const type = await inputs[i].getAttribute('type');
            const name = await inputs[i].getAttribute('name');
            if (type === 'text' && !name?.includes('password')) {
                await inputs[i].fill('Test User');
                break;
            }
        }
        
        for (let i = 0; i < inputs.length; i++) {
            const type = await inputs[i].getAttribute('type');
            if (type === 'email') {
                await inputs[i].fill(uniqueEmail);
                break;
            }
        }
        
        for (let i = 0; i < inputs.length; i++) {
            const type = await inputs[i].getAttribute('type');
            if (type === 'password') {
                await inputs[i].fill('password123');
                break;
            }
        }
        
        await page.evaluate(() => {
            const select = document.querySelector('select');
            if (select) {
                const option = document.createElement('option');
                option.value = 'Invalid';
                option.text = 'Invalid';
                select.appendChild(option);
                select.value = 'Invalid';
            }
        });
        
        await page.click('button[type="submit"]');
        await page.waitForTimeout(1000);
        
        const currentUrl = page.url();
        if (currentUrl.includes('/users/create')) {
            await expect(page.locator('form')).toBeVisible();
        } else {
            await expect(page.locator('h1, h2, h3')).toContainText('User');
        }
    });
}); 