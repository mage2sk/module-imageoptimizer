# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.10] - 2026-10-03

### Fixed
- Lazy loading reads the loading and fetchpriority attributes of an image properly, so text such as "loading=lazy" inside an alt or title value is no longer mistaken for the attribute or rewritten.
- A value of 0 for Threshold, Preload Image Count and Exclude Count is now honoured instead of falling back to the default.
