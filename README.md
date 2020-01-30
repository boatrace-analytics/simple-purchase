# Boatrace Analytics Simple Purchaser

[![Latest Stable Version](https://poser.pugx.org/boatrace-analytics/simple-purchaser/v/stable)](https://packagist.org/packages/boatrace-analytics/simple-purchaser)
[![Latest Unstable Version](https://poser.pugx.org/boatrace-analytics/simple-purchaser/v/unstable)](https://packagist.org/packages/boatrace-analytics/simple-purchaser)
[![License](https://poser.pugx.org/boatrace-analytics/simple-purchaser/license)](https://packagist.org/packages/boatrace-analytics/simple-purchaser)

## Installation
```
$ composer require boatrace-analytics/simple-purchaser
```

## Usage
```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Boatrace\Analytics\SimplePurchaser;

SimplePurchaser::setDepositAmount(1000)
    ->setSubscriberNumber('xxxxxxxx')
    ->setPersonalIdentificationNumber('xxxx')
    ->setAuthenticationPassword('xxxxxx')
    ->setPurchasePassword('xxxxxx')
    ->purchase(24, 12, [123, 124, 125, 126]);
```

## License
The Boatrace Analytics Simple Purchaser is open source software licensed under the [MIT license](LICENSE).
