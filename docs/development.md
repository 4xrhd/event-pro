# EventPro Developer Guide

Welcome to the EventPro development team. This document covers contribution standards, coding style, and local setup.

## Local Development Setup

1. **Clone and Install**:
   ```bash
   git clone https://github.com/4xrhd/event-pro.git
   cd event-pro
   composer install
   ```

2. **Environment Configuration**:
   Copy or configure your local database connection parameters in `config/database.php`.

3. **Running Tests**:
   Execute the test suite located in `/scratch`:
   ```bash
   php scratch/test_suite.php
   ```

## Coding Standards & Conventions
- **PHP Standards**: Adhere to PSR-12 coding style guidelines.
- **Strict Types**: Use `declare(strict_types=1);` where applicable.
- **MVC Separation**: Keep controllers lean. Business logic belongs in models or service classes. Views should contain minimal procedural PHP logic.
- **Security**: Always use prepared statements via PDO to prevent SQL injection. Escape all user output in views to prevent XSS.
