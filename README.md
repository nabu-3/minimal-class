# nabu-3 Minimal Class library
[![GitHub](https://img.shields.io/github/license/nabu-3/minimal-class.svg)](https://opensource.org/licenses/Apache-2.0)
[![Build Status](https://travis-ci.org/nabu-3/minimal-class.svg?branch=master)](https://travis-ci.org/nabu-3/minimal-class)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_minimal-class&metric=alert_status)](https://sonarcloud.io/dashboard?id=nabu-3_minimal-class)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_minimal-class&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=nabu-3_minimal-class)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_minimal-class&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=nabu-3_minimal-class)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_minimal-class&metric=security_rating)](https://sonarcloud.io/dashboard?id=nabu-3_minimal-class)

This is a base implementation of classes, interfaces and traits used to build all the other libraries provided by nabu-3 project. You can use this library at your convenience and stay out of the nabu-3 core framework to allow other nabu-3 projects to live independently and to be used outside this framework.

Package is provided under [Apache 2.0 license](https://github.com/nabu-3/minimal-class/blob/master/LICENSE) and you can use it under those terms.
## Install package
The package is deployed using composer and packagist and you can install it with this command:
```sh
composer require nabu-3/minimal-class
```
## How to use in your project
To use this package, you only need to include the autoload file of vendor folder:
```php
require_once 'vendor/autoload.php'
```
