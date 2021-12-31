# Laravel roles and permissions


## Introduction
This package allows you to assign roles and permissions to any laravel model, or on a pivot table (`many to many relationship`).

## Quick Samples

Below are samples of how to use the pacakge after installation. 

### On a model
The example below is done on a `User` model, but will also work on any model class.
```
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;

// Assign a 'Super Admin' role to this user
$user->assign(Role::SuperAdmin);

// Check if the user has the role
$user->hasRole(Role::SuperAdmin); 

// Check if the user can perform a operation
$user->can(Permission::DeleteTransactions);

// Check if the user has multiple permissions
$user->has(Permission::DeleteTransactions, Permission::BlockUsers);

// Authorize permissions
// This will throw an exception if the user does not have the permissions
$user->authorize(Permission::DeleteTransactions, Permission::BlockUsers);

// Authorize role
// This will throw an exception if the user does not have the permissions
$user->authorizeRole(Role::SuperAdmin);
```

### On a pivot table (many to many relationship)
This demonstrates how to use the package on a many to may relationship. 
 
In this example, we assume we have a `merchant` relationship in our `User` model.  And this relationship returns an instance of Laravel's `BelongsToMany` class.

```
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;

// Sample merchant
$merchant = Merchant::where('name', 'wallmart')->first();

// Assign a 'Super Admin' role to this user on the selected merchant (wallmart)
$user->of($merchant)->assign(Role::SuperAdmin);

// Check if the user has a super admin role on the selected merchant (wallmart)
$user->of($merchant)->hasRole(Role::SuperAdmin); 

// Check if the user can 'delete transactions' on the selected merchant (wallmart)
$user->of($merchant)->can(Permission::DeleteTransactions);

// Check if the user has multiple permissions on the selected merchant (wallmart)
$user->of($merchant)->has(Permission::DeleteTransactions, Permission::BlockUsers);

// Authorize permissions
// Throws an exception if the user does not have these permissions on the selected merchant (wallmart)
$user->of($merchant)->authorize(Permission::DeleteTransactions, Permission::BlockUsers);

// Authorize role
// Throws an exception if the user does not have the permissions on the selected merchant (wallmart)
$user->of($merchant)->authorizeRole(Role::SuperAdmin);
```

##  Requirements
- PHP 8.0 or higher
- Laravel 8.0 or higher
- Upon installation, the package publishes a `config/roles-and-permissions.php` file, ensure you do not have a file with the same name in your config directory.

## Installation
You can install the package via composer:
```
composer require tarzan-codes/roles-and-permissions
```

> *(Not compulsory)* If you have existing pivot tables that you want to apply the package to, you can add these table names to the `pivot > tables` array in the `config/roles-and-permissions.php` config file. This will add a `role` column to each of the tables. 

After successful installation, run the command below
```
php artisan roles:install
```

The above command does the following:

- Publishes a configuration file  `config/roles-and-permissions.php`
- Creates a `app\Enums\Role.php` file 
- Creates a `app\Enums\Permission.php` file
- Creates a `model_role` table which will be used to link `models` and `roles`
- Adds a `role` column to every pivot table listed in the `pivot > tables` array on the `config/roles-and-permissions.php` (if any).


## The Role Class
You define the roles in the `app\Enums\Role.php` class created as constants, and then assign `permissions` to them in the `permissions()` method that exists in the class.

Below is a sample of a `app\Enums\Role.php` class:

```
<?php

namespace  App\Enums;

use Tarzancodes\RolesAndPermissions\Helpers\BaseRole;

final class Role extends BaseRole
{
	const SuperAdmin = 'super_admin';
	const Admin = 'admin';
	const Customer = 'customer';
	
	/**
	* Set available roles and their permissions.
	*
	* @return  array
	*/
	final  public  static  function  permissions():  array
	{
		return [
			self::SuperAdmin  => [
				// Super Admin permissions should be here
				Permission::DeleteProducts, Permission::DeleteTransactions, Permission::ViewTransactions,
			],
		
			self::Admin  => [
				// Admin permissions should be here
				Permission::EditProducts, Permission::CreateProducts, Permission::MarkAsSoldOut
			],

			self::Customer  => [
				// Customer permissions should be here
				Permission::BuyProducts,
			],
		];
	}
}
```

From the above class, the constants  `SuperAdmin`, `Admin` and `Customer` are the declared roles, and their permissions are as follows:

- The `SuperAdmin` role has been given permission to  'delete products', 'delete transactions' and 'view transactions'.
- The `Admin` role has been given permissions to 'edit products', 'create products', and 'mark as sold out'
- And the `Customer` only has permission to 'buy products'

> Note: The roles declared only have the permissions assigned to them, i.e the SuperAdmin role is NOT given permissions of the Admin, and Customer roles, and vice-versa. 
*To make the senior roles have the lower roles permissions, visit the [use hierarchy](https://blah.com) section.*

*You can decide to set the roles constants to an integer value. Like the example below:*

```
// ...
	const SuperAdmin = 1;
	const Admin = 2;
	const Customer = 3;
// ...
```

> It is considered good practice to use `strings` instead of `integers` for better readablity

### Using the Role class
**Get all roles**
```
Use app\Enums\Role.php

$roles = Role::all(); // returns an array of all roles
```

**Get permissions of a specific role**
```
Use app\Enums\Role.php

$roles = Role::getPermissions(Role::SuperAdmin); // returns every permissions available to the super admin role as an array
```

## The Permission Class
The packge also ships with a `app\Enums\Permission.php` class. The class is a enum class where you should declare all the permissions required for app to work.

Below is an example of what the class will look like, using the same example as the `app\Enums\Role.php`  class above:

```
<?php

namespace  App\Enums;

use Tarzancodes\RolesAndPermissions\Helpers\BasePermission;

final class Permission extends BasePermission
{
	const DeleteProducts = 'delete_products';
	const DeleteTransactions = 'delete_transactions';
	const ViewTransactions = 'view_transactions';
	const EditProducts = 'edit_products';
	const MarkAsSoldOut = 'mark_as_sold_out';
	const BuyProducts = 'buy_products';
	
```

As explained above, this class lists all the permissions available. Instead of using `snake_case` for the values, you can use any case of your choice.

You can get all the available permissions:
```
use App\Enums\Permission;

$permissions = Permission::all(); // returns an array of all the permissions

```

*You can set the permissions constants to an integer value.*

## Basic Usage
After installation, add the `Tarzancodes\RolesAndPermissions\HasRoles` trait  to the model you want to use the package on.

Let's use a `User` model as an example:

`app\Models\User.php`
```
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tarzancodes\RolesAndPermissions\HasRoles;

class User extends Authenticatable 
{  
	use HasRoles;  
	// ...  
}
```  

An instance of a User class can now be assigned roles, and then have the permissions associated with the roles they are assigned.



In this example, we assume we have a `merchant_user` pivot table used as an intermediate table to link users and merchants. 

Below is a sample of the database structure we have:
```
users
    id - integer
    name - string

merchant
    id - integer
    name - string

merchant_user
    merchant_id - integer
    user_id - integer
    role - string
```




```
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;

// Assigning a "Super Admin" role to this user
$user->assign(Role::SuperAdmin);

// Check if the user has the role
$user->hasRole(Role::SuperAdmin); 

// Check if the user has a permission
$user->can(Permission::DeleteTransactions);

// Or
$user->has(Permission::DeleteTransactions);
```
