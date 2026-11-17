# Release instructions

## Repository

- Repository: `ITMOSOFT/laravel-localization`
- Composer package: `itmosoft/laravel-localization`
- Description: `Locale registry, persistent subject preferences, and request locale resolution middleware for Laravel applications.`

## Validate locally

```bash
composer install
composer test
```

Do not tag or publish until PHPUnit and GitHub Actions pass.

## Create repository and push (run locally)

Create a public repository named `laravel-localization` under ITMOSOFT, then run:

```bash
git init
git add .
git status
git commit -m "feat: add Laravel localization package"
git branch -M main
git remote add origin https://github.com/ITMOSOFT/laravel-localization.git
git push -u origin main
```

## Tag after tests and CI pass

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## Packagist

1. Sign in to Packagist.
2. Submit `https://github.com/ITMOSOFT/laravel-localization`.
3. Enable the GitHub service hook if prompted.
4. Confirm Packagist detects `itmosoft/laravel-localization` and `v1.0.0`.

## GitHub Release

Title: `v1.0.0 - Initial Release`

Use the full release notes included in the delivery message.
