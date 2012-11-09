Xsolla PHP SDK
=====================

[![Build Status](https://secure.travis-ci.org/xsolla/xsolla-sdk-php.png)](http://travis-ci.org/xsolla/xsolla-sdk-php)

## Installation

Using [Composer](http://getcomposer.org) is the recommended way to install Xsolla PHP SDK.

1\. Download and install Composer.

``` bash
$ cd path/to/your/project
$ curl -s "http://getcomposer.org/installer" | php
```

2\. Add this in your project's `composer.json` file.

``` json
{
    "require": {
        "xsolla/xsolla-sdk-php": "dev-master"
    }
}
```

3\. Install dependencies.

``` bash
$ php composer.phar install
```

4\. Require Composer's autoloader.

``` php
require '/path/to/your/project/vendor/autoload.php';
```

## Usage

#### [Mobile Payment API](http://xsolla.com/docs/mobile-payment-api)

``` php
use Xsolla\Sdk\Api\Client\Client;
use Xsolla\Sdk\Api\MobilePayment;

$mobilePayment = new MobilePayment(
    new Client,
    '/path/to/your/project/vendor/xsolla/xsolla-sdk-php/Xsolla/Sdk/Resources/schema/api',
    4783,//demo project
    'key'//secret key for demo project
);
$paymentAmountInRub = $mobilePayment->calculateSum(
    '9120000000',//example phone number
    10,//virtual currency amount
);
$invoiceNumber = $mobilePayment->invoice(
    '9120000000',//example phone number
    'demo',//example v1(User ID)
    null,//v2
    null,//v3
    100,//payment amount
);
```

## Tests

To run the test suite, you need [composer](http://getcomposer.org) and [PHPUnit](https://github.com/sebastianbergmann/phpunit).

``` bash
$ php composer.phar install
$ phpunit
```