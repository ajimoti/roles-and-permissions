# Implement roles and permissions on a laravel pivot table

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tarzancodes/roles-and-permissions.svg?style=flat-square)](https://packagist.org/packages/tarzancodes/roles-and-permissions)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/tarzancodes/roles-and-permissions/run-tests?label=tests)](https://github.com/tarzancodes/roles-and-permissions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/tarzancodes/roles-and-permissions/Check%20&%20fix%20styling?label=code%20style)](https://github.com/tarzancodes/roles-and-permissions/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/tarzancodes/roles-and-permissions.svg?style=flat-square)](https://packagist.org/packages/tarzancodes/roles-and-permissions)

---
This repo can be used to scaffold a Laravel package. Follow these steps to get started:

1. Press the "Use template" button at the top of this repo to create a new repo with the contents of this roles-and-permissions
2. Run "php ./configure.php" to run a script that will replace all placeholders throughout all the files
3. Remove this block of text.
4. Have fun creating your package.
5. If you need help creating a package, consider picking up our <a href="https://laravelpackage.training">Laravel Package Training</a> video course.
---

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/roles-and-permissions.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/roles-and-permissions)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require tarzancodes/roles-and-permissions
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="roles-and-permissions_without_prefix-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="roles-and-permissions_without_prefix-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="example-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$roles-and-permissions = new Tarzancodes\RolesAndPermissions();
echo $roles-and-permissions->echoPhrase('Hello, Tarzancodes!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ajibukunoluwa](https://github.com/tarzancodes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
