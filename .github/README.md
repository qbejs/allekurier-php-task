# GitHub Actions CI/CD Pipeline

## Overview

This repository uses GitHub Actions for continuous integration and deployment. The pipeline ensures code quality, security, and reliability through automated testing and analysis.

## Workflows

### 1. CI/CD Pipeline (`ci.yml`)

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
- **Rector** code modernization (dry run)
- **PHPUnit** unit and integration tests
- **Code coverage** reporting

#### Security Check
- **Composer audit** for dependency vulnerabilities
- **Production dependencies** only

#### Docker Build Test
- **Docker image** build verification
- **Docker Compose** configuration validation

### 2. Deploy to Production (`deploy.yml`)

**Triggers:**
- Push to `main` branch (after successful CI/CD pipeline)
- Manual workflow run

**Jobs:**

#### Deploy to Production
- **Production environment** setup
- **Cache optimization** and warmup
- **Configuration validation**
- **Deprecation checks**
- **Deployment notification**

## Features

### 🔄 Automated Testing
- **Unit Tests**: 39 tests, 117 assertions
- **Integration Tests**: Database and Redis integration
- **Event-Driven Tests**: Email sending verification
- **Domain Tests**: Business logic validation

### 📊 Code Quality
- **PHP CS Fixer**: PSR-12 + Symfony standards
- **PHPStan**: Level 8 static analysis
- **Rector**: Code modernization suggestions
- **Coverage**: Code coverage reporting

### 🔒 Security
- **Composer Audit**: Dependency vulnerability scanning
- **Production Build**: Security-focused deployment
- **Environment Validation**: Configuration verification

### 🐳 Docker Integration
- **Multi-stage builds**: Optimized production images
- **Service validation**: MySQL, Redis, PHP
- **Compose testing**: Configuration verification

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

### CI/CD Pipeline
The pipeline runs automatically on:
1. **Push to main/develop** - Full CI/CD
2. **Pull Request** - Tests and analysis
3. **Manual trigger** - On-demand execution

### Deployment
Production deployment is triggered automatically when:
- ✅ CI/CD pipeline passes
- ✅ Push to main branch
- ✅ All tests pass
- ✅ Security checks pass

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

## Troubleshooting

### Common Issues

#### Database Connection
```bash
# Check MySQL service
mysqladmin ping -h"127.0.0.1" -P"3306"

# Check Redis service
redis-cli -h 127.0.0.1 -p 6379 ping
```

#### Cache Issues
```bash
# Clear cache
php bin/console cache:clear --env=test

# Warm up cache
php bin/console cache:warmup --env=prod
```

#### Test Failures
```bash
# Run specific test
php bin/phpunit tests/Unit/Core/User/Domain/UserTest.php

# Run with coverage
php bin/phpunit --coverage-text --coverage-filter=src/
```

### Debug Commands
```bash
# Validate configuration
php bin/console debug:config --env=test

# Check deprecations
php bin/console debug:container --env=prod --deprecations

# Validate composer
composer validate --strict
```

## Contributing

### Before Committing
1. **Run tests locally**: `php bin/phpunit`
2. **Check code style**: `composer cs-check`
3. **Static analysis**: `composer phpstan`
4. **All checks**: `composer static-analysis`

### Pull Request Process
1. **Create feature branch** from develop
2. **Write tests** for new functionality
3. **Update documentation** if needed
4. **Submit PR** to develop branch
5. **CI/CD pipeline** runs automatically
6. **Code review** and approval
7. **Merge to develop** when ready
8. **Deploy to production** from main

## Support

For issues with the CI/CD pipeline:
1. Check the **Actions tab** in GitHub
2. Review **workflow logs** for errors
3. Verify **environment setup**
4. Contact **development team**

---

**🚀 Happy coding with confidence!**
