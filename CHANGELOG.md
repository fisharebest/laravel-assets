CHANGELOG
=========

## 2.0.0 (2018-12-06)
 - Update to latest version of CSS minifier
 - Add PHP 7.2, 7.3, 7.4 to the test matrix
 - Remove support for PHP 5.5
 - Apply PSR12 coding standard

## 1.6.1 (2017-09-01)
 - Fix: Support automatic package discovery for Laravel 5.5

## 1.6.0 (2017-08-14)
 - Fix #13 - Allow config to be serialized in `artisan config:cache`
 - Support automatic package discovery for Laravel 5.5

## 1.5.2 (2017-07-31)
 - Merge #11 - recognise protocol-agnostic URLs

## 1.5.1 (2017-06-29)
 - Fix #10 - upstream changes to mrclay/minify between 3.0.0 and 3.0.1

## 1.5.0 (2017-05-05)
 - Update dependencies to support PHP 7.1

## 1.4.2 (2017-02-20)
 - Use our own version of PHPUnit in Travis
 - Laravel 5.4 compatibility

## 1.4.1 (2016-02-29)
 - Do not purge non-asset files, such as `min/.gitignore`

## 1.4.0 (2016-02-04)
 - Add support for asset paths beginning `../`

## 1.3.0 (2016-02-04)
 - Add callback/hook after asset file creation

## 1.2.1 (2016-02-02)
 - Fix #4 - artisan command incompatible with Laravel 5.0

## 1.2.0 (2015-11-13)
 - Fix #1 - render small asset files inline

## 1.1.0 (2015-11-13)
 - Fix #3 - allow alternatives to file_get_contents()
 - Some dependencies only needed in development.
 - Update example collections.

## 1.0.0 (2015-11-02)
 - First public release, with unit tests and documentation.
