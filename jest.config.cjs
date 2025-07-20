module.exports = {
    moduleFileExtensions: ['js', 'json', 'vue'],
    transform: {
        '^.+\\.vue$': '@vue/vue3-jest',
        '^.+\\.js$': 'babel-jest',
    },
    testEnvironment: 'jsdom',
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/resources/js/$1',
    },
    testEnvironmentOptions: {
        customExportConditions: ["node", "node-addons"],
    },
    testMatch: [
        '<rootDir>/resources/tests/js/**/*.spec.js',
        '<rootDir>/resources/tests/js/**/*.test.js'
    ],
    testPathIgnorePatterns: [
        '<rootDir>/tests/Playwright/',
        '<rootDir>/resources/tests/e2e/'
    ],
    collectCoverageFrom: [
        'resources/js/**/*.{js,vue}',
        '!resources/js/bootstrap.js',
        '!resources/js/app.js'
    ],
};