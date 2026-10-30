# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.12] - 2026-10-30

### Changed
- Released under the MIT License: this module is free and open-source software.

## [1.0.11] - 2026-10-04

### Fixed
- Custom elements whose tag name starts with "img-" (for example an img-comparison-slider web component) are no longer treated as images: they no longer receive loading or fetchpriority attributes that broke their markup, and they no longer use up the above-the-fold eager image budget.
- In Stores > Configuration > Image Optimizer, settings that depend on Enable Image Optimizer, Enable WebP Detection or Enable Lazy Loading are now hidden when that switch is No, instead of staying visible on their own.
