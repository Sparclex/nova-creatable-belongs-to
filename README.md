# Creatable BelongsTo Field for Laravel Nova
BelongsTo Field for [Laravel Nova](https://nova.laravel.com) which allows to choose or create a resource

[![Latest Stable Version](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/v/stable)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![Total Downloads](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/downloads)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![Latest Unstable Version](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/v/unstable)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![License](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/license)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![StyleCI](https://github.styleci.io/repos/163976480/shield?branch=master)](https://github.styleci.io/repos/163976480)


## Installation

# Creatable BelongsTo Field for Laravel Nova
BelongsTo Field for [Laravel Nova](https://nova.laravel.com) which allows to choose or create a resource

[![Latest Stable Version](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/v/stable)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![Total Downloads](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/downloads)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![Latest Unstable Version](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/v/unstable)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![License](https://poser.pugx.org/sparclex/nova-creatable-belongs-to/license)](https://packagist.org/packages/sparclex/nova-creatable-belongs-to)
[![StyleCI](https://github.styleci.io/repos/163976480/shield?branch=master)](https://github.styleci.io/repos/163976480)

## Use Case

Ever had the following database structure and did not want the user to create the related resource (product_types) seperatly since it only consists of a unique name. But you don't want to put the product_type directly in the products table, since it would violate the third normal form.

![Database setup example](https://github.com/Sparclex/screenshots/blob/master/nova-creatable-belongs-to-database.png)

This package solves this, by keeping the original belongsTo field but allowing the user to create a new resource by simply entering a not yet existing name. Additionally it includes the [prepopulate-searchable](https://github.com/alexbowers/nova-prepopulate-searchable) package from alexbowers.

## Installation

```
composer require sparclex/nova-creatable-belongs-to
```

Add field inside your fields Array. The parameters consist of the same ones as the laravel nova [belongsTo field](https://nova.laravel.com/docs/1.0/resources/relationships.html#belongsto) with an additional `nameAttribute` which determines the name of the display attribute in the related model (default: name). 

```php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),
        CreatableBelongsTo::make('Recipient')
            ->prepopulate()
    ];
}
```

## TODO
- [ ] Tests
- [ ] Respect policies for creation


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


## TODO
- [ ] Tests
- [ ] Respect policies for creation


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
