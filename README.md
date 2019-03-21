# Yii2 Dependency Intection Bootstraping
[![Build Status](https://travis-ci.org/Horat1us/yii2-di-bootstrap.svg?branch=master)](https://travis-ci.org/Horat1us/yii2-di-bootstrap)
[![codecov](https://codecov.io/gh/Horat1us/yii2-di-bootstrap/branch/master/graph/badge.svg)](https://codecov.io/gh/Horat1us/yii2-di-bootstrap)

This package provides abstract bootstrap for Yii2 dependency injection container.
It have to be extended in packages with specifying available for configuration dependencies.

Main purpose of this package to prevent invalid container configuration.

## Installation
Using [packagist.org](https://packagist.org/packages/horat1us/yii2-di-bootstrap):
```bash
composer require horat1us/yii2-di-bootstrap:^1.0
```

## Usage

### Implement DI Bootstrap in your package
```php
<?php

namespace Package;

use Horat1us\Yii\DI;

class Bootstrap extends DI\Bootstrap
{
    public function getDefinitions() : array{
        return [
            Package\ConfigInterface::class => Package\Config::class,
        ];
    }
}
```

### Append package Bootstrap to your application configuration
```php
<?php
// config.php

use Package;

return [
    'bootstrap' => [
        'package' => [
            'class' => Package\Bootstrap::class,
            'definitions' => [
                // here you can reconfigure config interface
                // note: another class names can not be configured here
            ],
        ],
    ],
    // ... another application configuration
];
```

## License
[MIT](./LICENSE)
