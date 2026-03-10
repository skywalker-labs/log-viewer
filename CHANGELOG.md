# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Toolkit Integration**: Fully integrated `skywalker-labs/toolkit` as the foundational dependency.
- **Actions Layer**: Introduced `Actions/` for core logic operations (`ClearLogsAction`, `PruneLogsAction`, `ExportLogsAction`) extending `Skywalker\Support\Foundation\Action`.
- **Standard Directory Structure**: Reorganized files to match canonical Laravel package patterns (`resources/`, `routes/`, `src/Support/`).

### Fixed
- **PHPStan Level 9**: Achieved 100% strict type safety (0 errors) across the entire codebase.
- **Modern Standards**: Enforced code style with Laravel Pint.
- **Service Provider**: Refined path registration and modernized provider inheritance using `PackageServiceProvider`.

### Changed
- **Filesystem Refactor**: Extended `Skywalker\Support\Filesystem\Filesystem`.
- **Breaking Change**: Renamed `Filesystem::delete()` and `LogViewer::delete()` to `deleteByDate()` to avoid naming conflicts with the base Filesystem.
- Moved `_docs/` to `docs/`.
- Moved `helpers.php` to `src/Support/helpers.php`.
- Moved routes to `routes/web.php`.
- Moved views to `resources/views/`.
- Moved translations to `resources/lang/` (migrated from JSON to PHP).
