# Contributing to Laravolt Metabase

Thank you for considering contributing to Laravolt Metabase! This document provides guidelines and instructions for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Contributing Guidelines](#contributing-guidelines)
- [Pull Request Process](#pull-request-process)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Documentation](#documentation)

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to the maintainers.

### Our Standards

- Use welcoming and inclusive language
- Be respectful of differing viewpoints and experiences
- Gracefully accept constructive criticism
- Focus on what is best for the community
- Show empathy towards other community members

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Laravel 8.0 or higher
- Git
- A Metabase instance for testing

### Types of Contributions

We welcome various types of contributions:

- **Bug Reports** - Help us identify and fix issues
- **Feature Requests** - Suggest new functionality
- **Code Contributions** - Implement fixes and features
- **Documentation** - Improve or add documentation
- **Testing** - Add or improve test coverage

## Development Setup

1. **Fork the Repository**
   ```bash
   git clone https://github.com/your-username/metabase.git
   cd metabase
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Set Up Testing Environment**
   ```bash
   cp .env.example .env
   # Configure your test Metabase instance
   ```

4. **Create a Branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/issue-description
   ```

## Contributing Guidelines

### Reporting Bugs

Before reporting a bug:

1. **Check existing issues** to avoid duplicates
2. **Use the latest version** to ensure the bug hasn't been fixed
3. **Provide detailed information**:
   - Steps to reproduce
   - Expected behavior
   - Actual behavior
   - Environment details (PHP version, Laravel version, etc.)
   - Error messages or stack traces

**Bug Report Template:**

```markdown
## Bug Description
Brief description of the issue

## Steps to Reproduce
1. Step one
2. Step two
3. Step three

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Environment
- PHP Version: 
- Laravel Version: 
- Package Version: 
- Metabase Version: 

## Additional Context
Any additional information, screenshots, or context
```

### Suggesting Features

Feature requests are welcome! Please:

1. **Check existing requests** to avoid duplicates
2. **Describe the use case** - why is this feature needed?
3. **Provide examples** of how it would be used
4. **Consider backwards compatibility**

**Feature Request Template:**

```markdown
## Feature Description
Brief description of the proposed feature

## Use Case
Why is this feature needed? What problem does it solve?

## Proposed Implementation
How do you envision this working?

## Examples
Code examples or mockups of how this would be used

## Additional Context
Any other relevant information
```

## Pull Request Process

### Before Submitting

1. **Ensure your code follows** the coding standards
2. **Add tests** for new functionality
3. **Update documentation** if needed
4. **Test your changes** thoroughly
5. **Rebase your branch** on the latest main branch

### Pull Request Guidelines

1. **Use a descriptive title** that summarizes the change
2. **Reference related issues** using keywords like "fixes #123"
3. **Provide detailed description** of what changed and why
4. **Include testing instructions** for reviewers
5. **Keep changes focused** - one feature/fix per PR

**Pull Request Template:**

```markdown
## Description
Brief description of the changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Related Issues
Fixes #(issue number)

## Testing
- [ ] Tests added/updated
- [ ] Manual testing completed
- [ ] All tests pass

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No breaking changes (or marked as such)
```

## Coding Standards

### PHP Standards

- Follow **PSR-12** coding standard
- Use **strict types** (`declare(strict_types=1);`)
- Add **type hints** for all parameters and return types
- Use **meaningful variable and method names**
- Add **PHPDoc blocks** for classes and public methods

### Code Style

```php
<?php

declare(strict_types=1);

namespace Laravolt\Metabase;

use InvalidArgumentException;

/**
 * Service class for handling Metabase operations.
 */
class MetabaseService
{
    /**
     * Generate embed URL for dashboard or question.
     *
     * @param int|null $dashboard Dashboard ID
     * @param int|null $question Question ID
     * @return string Generated embed URL
     * @throws InvalidArgumentException When parameters are invalid
     */
    public function generateEmbedUrl(?int $dashboard, ?int $question): string
    {
        // Implementation
    }
}
```

### Laravel Conventions

- Use **Eloquent relationships** appropriately
- Follow **Laravel naming conventions**
- Use **dependency injection** where possible
- Utilize **Laravel's built-in features** (validation, caching, etc.)

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage

# Run specific test file
./vendor/bin/phpunit tests/Unit/MetabaseServiceTest.php
```

### Writing Tests

- **Write tests** for all new functionality
- **Update tests** when modifying existing code
- Use **descriptive test method names**
- Follow **AAA pattern** (Arrange, Act, Assert)

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Laravolt\Metabase\MetabaseService;

class MetabaseServiceTest extends TestCase
{
    public function test_it_generates_valid_embed_url_for_dashboard(): void
    {
        // Arrange
        $service = new MetabaseService();
        $dashboardId = 1;
        
        // Act
        $url = $service->generateEmbedUrl($dashboardId, null);
        
        // Assert
        $this->assertStringContains('/embed/dashboard/', $url);
    }
}
```

### Test Categories

- **Unit Tests** - Test individual classes/methods in isolation
- **Feature Tests** - Test complete features end-to-end
- **Integration Tests** - Test integration with Laravel and Metabase

## Documentation

### Code Documentation

- **Document all public methods** with PHPDoc
- **Include parameter types** and descriptions
- **Document exceptions** that may be thrown
- **Provide usage examples** for complex functionality

### User Documentation

- **Update README.md** for user-facing changes
- **Add examples** for new features
- **Update troubleshooting** section for common issues
- **Keep API reference** current

## Review Process

### What We Look For

- **Code quality** and adherence to standards
- **Test coverage** for new functionality
- **Documentation** completeness
- **Backwards compatibility** considerations
- **Performance** implications

### Feedback and Iteration

- Be **responsive to feedback** from reviewers
- **Ask questions** if feedback is unclear
- **Make requested changes** promptly
- **Test changes** after addressing feedback

## Release Process

### Versioning

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR** version for incompatible API changes
- **MINOR** version for backwards-compatible functionality
- **PATCH** version for backwards-compatible bug fixes

### Changelog

All notable changes are documented in `CHANGELOG.md`:

- **Added** for new features
- **Changed** for changes in existing functionality
- **Deprecated** for soon-to-be removed features
- **Removed** for now removed features
- **Fixed** for any bug fixes
- **Security** for vulnerability fixes

## Getting Help

If you need help with contributing:

1. **Check existing documentation** and issues
2. **Ask questions** in issue discussions
3. **Contact maintainers** directly for complex issues

## Recognition

Contributors will be recognized in:

- **README.md** contributors section
- **Release notes** for significant contributions
- **GitHub contributors** page

Thank you for contributing to Laravolt Metabase! Your efforts help make this package better for everyone.