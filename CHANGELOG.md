# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-07-29

### Added
- Initial release of the Exchange SDK
- Static methods for fetching current and historical exchange rates
- Support for both JSDelivr and Cloudflare endpoints with automatic fallback
- Methods for getting rates for specific currency pairs
- Support for minified and regular JSON responses
- Comprehensive error handling and null safety
- Configurable endpoint preferences
- Custom Guzzle client configuration support
- Full test suite with mock HTTP responses
- Basic usage examples and documentation

### Features
- `getLatestRates()`: Fetch current rates for any base currency
- `getHistoricalRates()`: Get historical rates for any date and currency
- `getLatestRate()`: Get current rate between two specific currencies
- `getHistoricalRate()`: Get historical rate between two currencies for a specific date
- Built-in fallback mechanism between JSDelivr and Cloudflare endpoints
- Support for both minified (.min.json) and regular (.json) formats
- Configurable preferred endpoint selection
- Custom HTTP client configuration

### Dependencies
- PHP 7.4 or higher
- Guzzle HTTP Client 7.0 or higher