import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './resources/tests/e2e',
    use: {
        baseURL: 'http://localhost:8000',
        browserName: 'chromium',
        headless: true,
    },
});