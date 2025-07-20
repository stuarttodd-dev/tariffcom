# Tariffcom Laravel Tech Test

This repository contains a Laravel-based technical assessment for candidates applying to Tariffcom. The project demonstrates your ability to work with a modern Laravel application, including setup, development, and testing practices.

---

## Quick Start

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
