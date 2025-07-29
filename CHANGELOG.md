# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2025-07-29

### Added
- PSR-16 Simple Cache implementation for caching responses
- Default filesystem cache implementation using Symfony Cache
- Ability to inject custom PSR-16 compatible cache implementations
- Cache duration configuration
- Cache key generation for different endpoints and currencies
- Cache clearing functionality

### Changed
- Updated dependencies to include PSR Simple Cache interface
- Updated documentation with caching examples
- Improved error handling for cache operations

## [1.0.0] - 2025-07-29

### Added
- Initial release of the Exchange SDK
- Static methods for fetching current and historical exchange rates
- Support for both JSDelivr and Cloudflare endpoints with automatic fallback
- Methods for getting rates for specific currency pairs
- Support for minified and regular JSON responses
- Configurable endpoint preferences
- Custom Guzzle client configuration support
- Full test suite with mock HTTP responses
- Basic usage examples and documentation