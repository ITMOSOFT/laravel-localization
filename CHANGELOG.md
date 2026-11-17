# Changelog

## v1.0.1 - 2026-11-17

- Fixed GitHub Actions dependency matrix by running Composer update with the selected `--prefer-lowest` or `--prefer-stable` flag.
- Removed unsupported `composer-options` input from `ramsey/composer-install`.
- Updated checkout action to v5 to avoid the Node.js 20 deprecation warning.

## v1.0.0 - 2026-11-17

- Initial release.
- Added configurable supported locale registry and fallback locale.
- Added polymorphic persisted locale preferences.
- Added request locale resolution middleware.
- Added route parameter, authenticated preference, and Accept-Language resolution.
