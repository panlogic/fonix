Fonix
==========

Fonix is a PHP library class to make using the Fonix API easy

[![Latest Stable Version](https://poser.pugx.org/panlogic/fonix/v/stable.svg)](https://packagist.org/packages/panlogic/fonix) [![Total Downloads](https://poser.pugx.org/panlogic/fonix/downloads.svg)](https://packagist.org/packages/panlogic/fonix) [![Latest Unstable Version](https://poser.pugx.org/panlogic/fonix/v/unstable.svg)](https://packagist.org/packages/panlogic/fonix) [![License](https://poser.pugx.org/panlogic/fonix/license.svg)](https://packagist.org/packages/panlogic/fonix)

## Composer

To install Fonix as a Composer package, simply add this to your composer.json:

```json
"panlogic/fonix": "dev-master"
```

..and run `composer update`.  Once it's installed, if you're using Laravel 5, you can register the service provider in `app/config/app.php` in the `providers` array add :

```php

	'Panlogic\Fonix\FonixServiceProvider',

```

You can also benefit from using a Fonix Facade in Laravel 5 by adding to the alias array also in app.php below the providers array

```php

	'Fonix'    => 'Panlogic\Fonix\Facades\FonixFacade',

```

## Documentation

If you're using Laravel, publish the config file by running

```php

	php artisan vendor:publish

```

This will create a panlogic.fonix.php file in your Config directory, be sure to fill in the appropriate details provided by Fonix.

If you aren't using Laravel then you can create a Fonix object by:

use Panlogic\Fonix\Fonix;

$config = [
	'live_apikey' 		=> 'your-live-api-key-here',
	'test_apikey' 		=> 'your-test-api-key-here',
	'platform' 			=> 'test',
	'originator' 		=> '123456', //short code
];

$fonix = new Fonix($config);

## Copyright and Licence

Fonix has been written by Panlogic Ltd and is released under the MIT License.
