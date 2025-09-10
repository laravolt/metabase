# Static Analysis with Larastan

This package uses [Larastan](https://github.com/larastan/larastan) (PHPStan for Laravel) for static analysis to ensure code quality and catch potential bugs before runtime.

## Configuration

The static analysis is configured at **level 9** (the highest level) in `phpstan.neon` for maximum code quality assurance.

### Version Compatibility

Due to Larastan's version constraints, different Laravel versions require different Larastan versions:

- **Laravel 8-11**: Uses Larastan `^2.0`
- **Laravel 12**: Uses Larastan `^3.1` (when available)

The package automatically handles these constraints in `composer.json`.

### Key Features

- **Level 9 Analysis**: The strictest level of static analysis
- **Laravel Integration**: Full Laravel framework support through Larastan
- **Type Safety**: Comprehensive type checking and validation
- **Generic Types**: Support for generic collections and arrays
- **Uninitialized Properties**: Detection of uninitialized class properties
- **Return Type Analysis**: Verification of method return types

## Running Static Analysis

### Local Development

```bash
# Run static analysis
composer phpstan

# Alternative commands
composer analyse
composer test-static
composer check

# Clear PHPStan cache
composer phpstan-clear

# Generate baseline (if needed)
composer phpstan-baseline
```

### Memory Usage

The analysis runs with 1GB memory limit by default. If you encounter memory issues:

```bash
# Increase memory limit
vendor/bin/phpstan analyse --memory-limit=2G
```

## Continuous Integration

Static analysis runs automatically on:
- Push to main/master/develop branches  
- Pull requests to main/master/develop branches
- Multiple PHP versions (8.2, 8.3)
- Multiple Laravel versions with compatible Larastan versions:
  - Laravel 10.x + Larastan 2.x
  - Laravel 11.x + Larastan 2.x  
  - Laravel 12.x + Larastan 3.x

## Configuration Details

### Analysis Paths
- `src/` - Main source code
- `config/` - Configuration files
- `routes/` - Route definitions

### Excluded Paths
- `vendor/` - Third-party packages
- `storage/` - Laravel storage directory
- `bootstrap/cache/` - Laravel cache files

### Bootstrap Files
- `phpstan-bootstrap.php` - Custom Laravel helper function definitions for static analysis

### Strict Rules Enabled
- `checkMissingIterableValueType` - Requires specific array/collection types
- `checkGenericClassInNonGenericObjectType` - Enforces generic type usage
- `checkUninitializedProperties` - Detects uninitialized class properties
- `checkTooWideReturnTypesInProtectedAndPublicMethods` - Ensures precise return types
- `checkImplicitMixed` - Prevents implicit mixed types

### Laravel-Specific Handling
- Ignores Laravel helper functions (`config()`, `view()`, `app()`)
- Handles Laravel facades and service container
- Supports Blade component analysis

## Benefits

1. **Early Bug Detection**: Catch type errors before runtime
2. **Better IDE Support**: Enhanced autocompletion and refactoring
3. **Documentation**: Type hints serve as inline documentation
4. **Refactoring Safety**: Confident code changes with type checking
5. **Team Consistency**: Enforced coding standards across the team

## Troubleshooting

### Common Issues

1. **Memory Errors**: Increase memory limit in composer scripts
2. **False Positives**: Add specific ignores to `phpstan.neon`
3. **Missing Types**: Add proper PHPDoc annotations
4. **Version Conflicts**: Ensure Laravel and Larastan versions are compatible:
   ```bash
   # For Laravel 10-11
   composer require --dev "larastan/larastan:^2.0"
   
   # For Laravel 12
   composer require --dev "larastan/larastan:^3.1"
   ```

### Baseline Usage

If you need to temporarily ignore existing issues:

```bash
composer phpstan-baseline
```

This creates a `phpstan-baseline.neon` file with current issues that will be ignored in future runs.

## Best Practices

1. **Run Before Commits**: Always run static analysis before committing
2. **Fix Issues Promptly**: Don't let static analysis issues accumulate
3. **Use Strict Types**: Leverage PHP 8.2+ features for better type safety
4. **Document Complex Types**: Use PHPDoc for complex generic types
5. **Review Ignores**: Regularly review and remove unnecessary ignores

## Integration with IDEs

Most modern IDEs support PHPStan integration:

- **PhpStorm**: Built-in support with PHPStan plugin
- **VS Code**: Use the PHPStan extension
- **Vim/Neovim**: Various PHPStan plugins available

This ensures real-time feedback while coding, not just during CI/CD.