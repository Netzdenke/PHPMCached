PHPMCached
==========

[![Latest Stable Version](https://poser.pugx.org/netzdenke/phpmcached/v/stable.svg)](https://packagist.org/packages/netzdenke/phpmcached)
[![License](https://poser.pugx.org/netzdenke/phpmcached/license.svg)](https://packagist.org/packages/netzdenke/phpmcached)

PHPMCached is a PHP Memcached adapter with support for cache groups.

Requirements
------------
+ PHP 5.3 or later
+ PHP Memcached extension

Usage
-----

Install the latest version with `composer require netzdenke/phpmcached`

```php
<?php

$cache = \PHPMCached\PHPMCached::getInstance('application_name');
$cacheKey = $cache->getCacheKey('foo');

$cache->set($cacheKey, 'value', 'cache_group', \PHPMCached\PHPMCached::EXPIRATION_HOUR);

$value = $cache->get($cacheKey);
```

License
-------
PHPMCached is licensed under the MIT License - see the `LICENSE` file for details