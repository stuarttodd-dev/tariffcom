# Tariffcom Laravel Tech Test

This repository contains a Laravel-based technical assessment for Tariffcom.

---

## Project Demo

<video width="100%" controls>
  <source src="http://137.184.25.10/demo.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>

*Note: If the video doesn't load, you can access it directly at [http://137.184.25.10/demo.mp4](http://137.184.25.10/demo.mp4)*

## Quick Start

You can log in to the application using the following test credentials:

- **Email:** test@example.com
- **Password:** password

### Prerequisites
- [Docker](https://www.docker.com/) and Docker Compose
- [Node.js](https://nodejs.org/) (for local development, optional if using Docker for everything)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone git@github.com:stuarttodd-dev/tariffcom.git
   cd tech-test/tariffcom
   ```

2. **Copy the example environment file and configure as needed**
   ```bash
   cp .env.example .env
   # Edit .env if you need to change DB credentials or app settings
   ```

3. **Start the application with Docker Compose**
   ```bash
   docker-compose build
   docker-compose up -d
   ```
   This will start the app, queue worker, and MySQL containers.

4. **Install PHP and JS dependencies**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

5. **Run database migrations and seeders**
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

6. **Access the application**
   - Visit: [http://localhost:8000](http://localhost:8000)

---

## Testing

- **PHP Tests:**
  ```bash
  docker-compose exec app composer test
  ```
- **JavaScript Tests:**
  ```bash
  docker-compose exec app npm test
  ```
- **End-to-End Tests (Playwright):**

Requires the following test credentials to work:

- **Email:** test@example.com
- **Password:** password
- 
  ```bash
  docker-compose exec app npm run test:e2e
  ```

### Code Standards & Quality

- **Check code standards (PHPStan, PHPMD, PHPCS, Rector):**
  ```bash
  docker-compose exec app composer standards:check
  ```
- **Auto-fix code style issues (where possible):**
  ```bash
  docker-compose exec app composer standards:fix
  ```

These scripts run static analysis, code style, and quality tools to help ensure your code meets project standards.

---

## Simulate the Full Pipeline Locally

You can run the full test and standards pipeline locally with:

```bash
./scripts/test-pipeline.sh
```

This script will:
- Run all PHP (Pest) tests
- Run all JavaScript (Jest) tests
- Check code standards (PHPStan, PHPMD, PHPCS, Rector)
- Run end-to-end tests (if configured)
- Print a summary of results just like a CI pipeline

---

## Potential Improvements
This section outlines potential enhancements.

- **Additional Playwright E2E Tests**: Expand test coverage to include user registration flow, profile management, and user CRUD operations
- **Unit Test Coverage**: Increase coverage for API resources, model relationships, and form request validation
- **Success Notifications**: Implement toast notifications for all CRUD operations and form submissions
- **Error Handling**: Improve user-friendly error messages and validation feedback
- **Static Analysis**: I baselined a lot of phpmd and phpstan issues, ideally they'd be resolved.
- **Enums**: I'd have used enums for types.
- **Policies / Gates**: We've got different user types but nothing around permissions.

---
