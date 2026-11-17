# v1.0.1 - Fix GitHub Actions Composer Matrix

## Fixed

- Corrected the GitHub Actions dependency matrix.
- Removed the unsupported `composer-options` input from `ramsey/composer-install@v3`.
- Runs `composer update --no-interaction --prefer-dist --prefer-lowest` in the lowest-dependency matrix job.
- Runs `composer update --no-interaction --prefer-dist --prefer-stable` in the stable-dependency matrix job.
- Updated `actions/checkout` to v5 to address the Node.js 20 deprecation notice.

## Notes

This patch changes CI workflow configuration only. It does not modify package runtime behavior.

Ubuntu runner migration notices are informational and are not the cause of the reported failure.
