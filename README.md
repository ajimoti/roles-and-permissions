# Laravel roles and permissions

## Introduction

This package allows you to assign roles and permissions to any laravel model, or on a pivot table (`many to many relationship`).

Built and written by [Ajimoti Ibukun](https://www.linkedin.com/in/ibukun-ajimoti-3420a786/)

## Quick Samples

### On a Model
The example below explains how to use the package on a model after installation.  

```php title="app\Http\Controllers\HomeController.php"
use App\Enums\Role;
use App\Enums\Permission;

// Assign a 'Super Admin' role to this user
$user->assign(Role::SuperAdmin);

// Check if the user has the role
$user->hasRole(Role::SuperAdmin);

// Check if the user can perform a operation
$user->can(Permission::DeleteTransactions);

// Check if the user has multiple permissions
$user->holds(Permission::DeleteTransactions, Permission::BlockUsers);
```

### Pivot table (many to many relationship)
This demonstrates how to use the package on a `many to many` relationship.
In this example, we assume we have a `merchant` relationship in our `User` model. And this relationship returns an instance of Laravel's `BelongsToMany` class.

Import the `App\Enums\Role` and `App\Enums\Permission` class.
```php title="app\Http\Controllers\MerchantController.php"
use App\Enums\Role;
use App\Enums\Permission;

// Sample merchant
$merchant = Merchant::where('name', 'wallmart')->first();

// Assign a 'Super Admin' role to this user on the selected merchant (wallmart)
$user->of($merchant)->assign(Role::SuperAdmin);

// Check if the user has a super admin role on the selected merchant (wallmart)
$user->of($merchant)->hasRole(Role::SuperAdmin);

// Check if the user can 'delete transactions' on the selected merchant (wallmart)
$user->of($merchant)->can(Permission::DeleteTransactions);

// Check if the user has multiple permissions on the selected merchant (wallmart)
$user->of($merchant)->holds(Permission::DeleteTransactions, Permission::BlockUsers);
```


>>We used the `user` model to make the example explanatory, similar to the examples above the package will work on any model class.


## Requirements
- PHP 8.0 or higher
- Laravel 8.0 or higher
- Upon installation, the package publishes a `config/roles-and-permissions.php` file, ensure you do not have a file with the same name in your config directory.

### Pros
- The package can be used on any model, i.e any model can be assigned roles, and permissions.
- Roles can be given multiple permissions.
- Models have permissions via roles.
- Models can be assigned multiple roles.
- A `many to many` relationship can be assigned roles. (i.e the package can be used on a pivot table).
- Supports role hierarchy. (A higher level role can be configured to have the permissions of lower level roles).

### Crons
- Permissions cannot be assigned directly on a `many to many` relationship.

## Installation
You can install the package via composer:
```bash
composer require ajimoti/roles-and-permissions
```

If you have existing pivot tables that you want to apply the package on, you can add the table names to the `pivot.tables` array in the `config/roles-and-permissions.php` config file. The command below will add a `role` column to every pivot table provided in the array.

Run the command below, then you are set to use the package.

```bash
php artisan roles:install
```

## Documentation
You can read the proper [documentation here](https://roles.ajimoti.com/docs/intro)
