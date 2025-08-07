# GitHub Actions CI/CD Pipeline

### 1. CI/CD Pipeline

**Triggers:**
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop` branches

**Jobs:**

#### Tests & Static Analysis
- **PHP 8.2** with required extensions (mbstring, intl, pdo_mysql, redis)
- **MySQL 5.7** service for database tests
- **Redis 6** service for cache tests
- **Composer** dependency management
- **PHP CS Fixer** code style checking
- **PHPStan** static analysis
- **PHPUnit** unit and integration tests
- **Code coverage** reporting

#### Security Check
- **Composer audit** for dependency vulnerabilities (temporary disabled due to lock with Symfony version)
- **Production dependencies** only

## Features

### 🔄 Automated Testing
- **Unit Tests**: 39 tests, 117 assertions
- **Integration Tests**: Database and Redis integration
- **Event-Driven Tests**: Email sending verification
- **Domain Tests**: Business logic validation

### 📊 Code Quality
- **PHP CS Fixer**: PSR-12 + Symfony standards
- **PHPStan**: Level 8 static analysis
- **Coverage**: Code coverage reporting

### 🔒 Security
- **Production Build**: Security-focused deployment
- **Environment Validation**: Configuration verification

### ⚡ Performance
- **Caching**: Composer, PHP CS Fixer, PHPStan
- **Parallel jobs**: Independent test suites
- **Optimized builds**: Production-ready artifacts

## Configuration

### Environment Variables
```bash
# Development
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL="mysql://user:pass@host:3306/db"

# Test
APP_ENV=test
DATABASE_URL="mysql://test_user:test_password@127.0.0.1:3306/test_db"

# Production
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL="mysql://prod_user:prod_password@prod_host:3306/prod_db"
```

### Services
- **MySQL**: 5.7 with health checks
- **Redis**: 6-alpine with health checks
- **PHP**: 8.2 with required extensions

## Usage

### Local Development
```bash
# Install dependencies
composer install

# Run tests
php bin/phpunit

# Code style check
composer cs-check

# Static analysis
composer phpstan

# All checks
composer static-analysis
```

## Monitoring

### Coverage Reports
- **Codecov integration** for coverage tracking
- **Coverage thresholds** for quality gates
- **Historical data** for trend analysis

### Quality Gates
- **Test coverage**: Minimum 80%
- **Static analysis**: No errors
- **Code style**: PSR-12 compliance
- **Security**: No vulnerabilities

### Notifications
- **Success**: Deployment confirmation
- **Failure**: Detailed error reporting
- **Coverage**: Coverage statistics
