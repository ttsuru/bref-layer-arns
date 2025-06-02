# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] - 2025-06-02
### Fixed
- Improved argument parsing to support quoted layer strings with spaces (e.g., "php-84-fpm gd-php-84 insights").

## [1.1.0] - 2025-06-02
### Added
- Support for `insights` and `arm-insights` layer types.
- Manual and automated testing with real AWS ARN formats.
- Added script to automatically fetch latest Lambda Insights ARNs from AWS documentation.
- Integrated Symfony HttpClient for robust network requests.
- Enhanced ARN parsing logic to support nested tags in HTML.
- Added GitHub Actions workflow to update `layers-insights.json` weekly.
- Automatically opens a pull request if updates are detected.
- Included `contents: write` permission for GitHub Actions.

## [1.0.0] - 2025-05-XX
### Added
- Initial release with layer resolution support via CLI.
- Supports BREF layers and extra-php-extensions.
