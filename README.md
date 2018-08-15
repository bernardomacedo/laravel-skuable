# Generate Skus when saving Eloquent models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/Bernardomacedo/laravel-Skuable.svg?style=flat-square)](https://packagist.org/packages/Bernardomacedo/laravel-Skuable)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Bernardomacedo/laravel-Skuable/master.svg?style=flat-square)](https://travis-ci.org/Bernardomacedo/laravel-Skuable)
[![Quality Score](https://img.shields.io/scrutinizer/g/Bernardomacedo/laravel-Skuable.svg?style=flat-square)](https://scrutinizer-ci.com/g/Bernardomacedo/laravel-Skuable)
[![StyleCI](https://styleci.io/repos/48512561/shield?branch=master)](https://styleci.io/repos/48512561)
[![Total Downloads](https://img.shields.io/packagist/dt/Bernardomacedo/laravel-Skuable.svg?style=flat-square)](https://packagist.org/packages/Bernardomacedo/laravel-Skuable)

This package provides a trait that will generate a unique Sku when saving any Eloquent model. 

```php
$model = new EloquentModel();
$model->name = 'activerecord is awesome';
$model->save();

echo $model->Sku; // ouputs "ACT-7655677"
```

The Skus are generated with Laravels `Str::Sku` method, whereby spaces are converted to '-'.

Bernardomacedo is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://Bernardomacedo.be/opensource).

## Installation

You can install the package via composer:
``` bash
composer require bernardomacedo/laravel-skuable
```

## Usage

Your Eloquent models should use the `Bernardomacedo\Skuable\HasSku` trait and the `Bernardomacedo\Skuable\SkuOptions` class.

The trait contains an abstract method `getSkuOptions()` that you must implement yourself. 

Here's an example of how to implement the trait:

```php
<?php

namespace App;

use Bernardomacedo\Skuable\HasSku;
use Bernardomacedo\Skuable\SkuOptions;
use Illuminate\Database\Eloquent\Model;

class YourEloquentModel extends Model
{
    use HasSku;
    
    /**
     * Get the options for generating the Sku.
     */
    public function getSkuOptions() : SkuOptions
    {
        return SkuOptions::create()
            ->generateSkusFrom('name')
            ->saveSkusTo('Sku');
    }
}
```

Want to use multiple field as the basis for a Sku? No problem!

```php
public function getSkuOptions() : SkuOptions
{
    return SkuOptions::create()
        ->generateSkusFrom(['first_name', 'last_name'])
        ->saveSkusTo('Sku');
}
```

You can also pass a `callable` to `generateSkusFrom`.


By default the package will generate unique Skus by appending '-' and a number, to a Sku that already exists.

You can disable this behaviour by calling `allowDuplicateSkus`.

```php
public function getSkuOptions() : SkuOptions
{
    return SkuOptions::create()
        ->generateSkusFrom('name')
        ->saveSkusTo('Sku')
        ->allowDuplicateSkus();
}
```

You can also put a maximum size limit on the created Sku:

```php
public function getSkuOptions() : SkuOptions
{
    return SkuOptions::create()
        ->generateSkusFrom('name')
        ->saveSkusTo('Sku')
        ->SkusShouldBeNoLongerThan(50);
}
```

You can also use a custom separator by calling `usingSeparator`

```php
public function getSkuOptions() : SkuOptions
{
    return SkuOptions::create()
        ->generateSkusFrom('name')
        ->saveSkusTo('Sku')
        ->usingSeparator('_');
}
```

To set the language used by `Str::Sku` you may call `usingLanguage`

```php
public function getSkuOptions() : SkuOptions
{
    return SkuOptions::create()
        ->generateSkusFrom('name')
        ->saveSkusTo('Sku')
        ->usingLanguage('nl');
}
```

The Sku may be slightly longer than the value specified, due to the suffix which is added to make it unique.

You can also override the generated Sku just by setting it to another value then the generated Sku.

```php
$model = EloquentModel:create(['name' => 'my name']); //Sku is now "my-name"; 
$model->Sku = 'my-custom-url';
$model-save(); //Sku is now "my-custom-url"; 
```

If you don't want to create the Sku when the model is initially created you can set use the `doNotGenerateSkusOnCreate() function.

```php
public function getSkuOptions() : SkuOptions
{
    return SkuOptions::create()
        ->generateSkusFrom('name')
        ->saveSkusTo('Sku')
        ->doNotGenerateSkusOnCreate();
}
```

Similarly, if you want to prevent the Sku from being updated on model updates, call `doNotGenerateSkusOnUpdate()`.

```php
public function getSkuOptions() : SkuOptions
{
    return SkuOptions::create()
        ->generateSkusFrom('name')
        ->saveSkusTo('Sku')
        ->doNotGenerateSkusOnUpdate();
}
```

This can be helpful for creating permalinks that don't change until you explicitly want it to.

```php
$model = EloquentModel:create(['name' => 'my name']); //Sku is now "my-name"; 
$model-save();

$model->name = 'changed name';
$model->save(); //Sku stays "my-name"
```

If you want to explicitly update the Sku on the model you can call `generateSku()` on your model at any time to make the Sku according to your other options. Don't forget to `save()` the model to persist the update to your database.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email bernardo.macedo@gmail.com instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)


Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/Bernardomacedo). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
