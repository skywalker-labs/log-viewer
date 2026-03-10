# Contributing to LogViewer

Thank you for considering contributing to LogViewer! To ensure the project remains high-quality and maintainable, please follow these guidelines.

## Code of Conduct

This project and everyone participating in it is governed by the [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs
- Use the **Bug Report** template when opening an issue.
- Provide a clear, descriptive title.
- Include steps to reproduce, expected behavior, and actual behavior.
- Attach screenshots if applicable.

### Suggesting Enhancements
- Use the **Feature Request** template.
- Explain why this enhancement would be useful to most users.

### Pull Requests
1. Fork the repository and create your branch from `main`.
2. Install dependencies: `composer install`.
3. If you've added code that should be tested, add tests.
4. Ensure the test suite passes: `composer test`.
5. Run static analysis: `vendor/bin/phpstan analyse`.
6. Follow the PSR-12 coding standard (enforced via Laravel Pint).
7. Write a descriptive pull request message.

## Style Guide

We use **Laravel Pint** to maintain a consistent code style. Before committing, please run:
```bash
vendor/bin/pint
```

## Static Analysis

We enforce **PHPStan Level 9**. Ensure your changes do not introduce type-safety regressions:
```bash
vendor/bin/phpstan analyse
```

## Financial Contributions

If you find LogViewer useful, consider [sponsoring the project](https://github.com/sponsors/ermradulsharma).
