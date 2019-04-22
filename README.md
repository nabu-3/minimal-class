# nabu-3 Minimal Class library
[![Build Status](https://travis-ci.org/nabu-3/minimal-class.svg?branch=master)](https://travis-ci.org/nabu-3/minimal-class)
This is a base implementation of classes, interfaces and traits used to build all the other libraries provided by nabu-3 project. You can use this library at your convenience and stay out of the nabu-3 core framework to allow other nabu-3 projects to live independently and to be used outside this framework.
Package is provided under [Apache 2.0 license](https://github.com/nabu-3/minimal-class/blob/master/LICENSE) and you can use it under those terms.
## Install package
The package is deployed using composer and packagist and you can install it with this command:
```sh
composer require nabu-3/minimal-class
```
To use included classes, you only need to include the autoload file of vendor folder:
```php
require_once 'vendor/autoload.php'
```
