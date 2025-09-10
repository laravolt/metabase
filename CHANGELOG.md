# Changelog

All notable changes to `laravolt/metabase` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Comprehensive documentation with API reference and troubleshooting guide
- Contributing guidelines for developers
- Changelog template for tracking releases

### Changed
- Enhanced README with detailed usage examples and configuration options
- Improved code documentation and type hints

### Deprecated
- Nothing

### Removed
- Nothing

### Fixed
- Nothing

### Security
- Nothing

## [Previous Releases]

> **Note:** This changelog starts from the current version. Previous release notes may be available in the GitHub releases page.

---

## Release Template

When creating a new release, copy the template below:

```markdown
## [X.Y.Z] - YYYY-MM-DD

### Added
- New features and capabilities

### Changed
- Changes in existing functionality

### Deprecated
- Soon-to-be removed features (still functional)

### Removed
- Now removed features

### Fixed
- Bug fixes

### Security
- Vulnerability fixes
```

## Guidelines for Maintainers

### Version Numbering
- **MAJOR** (X.0.0): Breaking changes, incompatible API changes
- **MINOR** (X.Y.0): New features, backwards-compatible
- **PATCH** (X.Y.Z): Bug fixes, backwards-compatible

### Categories
- **Added**: New features, dependencies, or capabilities
- **Changed**: Changes in existing functionality or behavior
- **Deprecated**: Features that will be removed in future versions
- **Removed**: Features that have been removed in this version
- **Fixed**: Bug fixes and corrections
- **Security**: Security improvements and vulnerability fixes

### Writing Guidelines
1. **Use present tense** ("Add feature" not "Added feature")
2. **Be specific** about what changed
3. **Include issue/PR numbers** when relevant
4. **Group related changes** together
5. **Highlight breaking changes** clearly
6. **Mention compatibility** requirements if changed

### Example Entries

```markdown
### Added
- Support for Metabase themes in embed URLs (#123)
- New `MetabaseService::validateConfiguration()` method
- Configuration validation in service provider boot process

### Changed
- Updated minimum PHP requirement to 8.2
- Improved error messages for invalid configurations
- Enhanced JWT token generation with better security

### Fixed
- Fixed issue with special characters in dashboard parameters (#456)
- Resolved iframe loading issues with certain Metabase versions
- Corrected type hints in MetabaseComponent class

### Security
- Updated JWT library to address security vulnerability
- Added input sanitization for embed parameters
```