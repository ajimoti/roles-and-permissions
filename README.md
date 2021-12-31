
# Laravel roles and permissions


## Introduction
This package allows you to assign roles and permissions to any laravel model, or on a pivot table (`many to many relationship`).

## Quick Samples

Below are samples of how to use the pacakge after installation. 

### Model class
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
```

### Pivot table (many to many relationship)
This demonstrates how to use the package on a `many to many` relationship. 
 
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
    			Permission::DeleteProducts, Permission::DeleteTransactions, Permission::ViewTransactions,
    		],
    	
    		self::Admin  => [
    			Permission::EditProducts, Permission::CreateProducts, Permission::MarkAsSoldOut
    		],
    
    		self::Customer  => [
    			Permission::BuyProducts,
    		],
    	];
	}
}
```

From the above class, the constants  `SuperAdmin`, `Admin` and `Customer` are the declared roles, and their permissions are as follows:

- The `SuperAdmin` role has been given permission to  `delete products`, `delete transactions` and `view transactions`
- The `Admin` role has been given permissions to `edit products`, `create products`, and `mark as sold out`
- And the `Customer` only has permission to `buy products`

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

**Get all roles and permissions**
```
Use app\Enums\Role.php 

$roles = Role::permissions(); // returns a multidimensional array of all roles and permissions
```

**Get permissions of a specific role**
```
Use app\Enums\Role.php

$roles = Role::getPermissions(Role::SuperAdmin); // returns every permissions available to the super admin role as an array
```

## The Permission Class
The package also ships with a `app\Enums\Permission.php` class. The `Permission` class is a enum class where you should declare all the permissions required for app to work.

Below is an example of what the class will look like:

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

As explained above, this class lists all the permissions available. You can use any case of your choice for the values, you are not required to use  the `snake_case`.

You can get all the available permissions:
```
use App\Enums\Permission;

$permissions = Permission::all(); // returns an array of all the permissions

```

*You can set the permissions constants to an integer value.*

## Basic Usage
After installation, add the `Tarzancodes\RolesAndPermissions\HasRoles` trait  to the model you want to use the package on.

Using the `User` model as an example:

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

An instance of a User class can now be assigned roles, and have the permissions associated with the roles they are assigned.

### Assigning roles
```
// give the user a super admin role
$user->assign(Role::SuperAdmin); // returns boolean

// or give the user multiple roles
$user->assign(Role::SuperAdmin, Role::Admin); // returns boolean
```

### Get roles
```
// Get user roles
$user->roles(); // returns an array of the user's roles
```

### Get Permissions
```
// Get user permissions
$user->permissions(); // returns an array of the user's permissions
```
>The `permissions()` method returns an array of all permissions associated to the roles the model has. For instance, if the `$user` model above has been assigned a `SuperAdmin` and `Admin` role, an array of both permissions will be returned.


### Has Role
Check if a model has a specific role, or multiple roles
```
$user->hasRole(Role::SuperAdmin); // returns boolean

// Or
$user->hasRole(Role::SuperAdmin, Role::Customer); // returns boolean
$user->hasRole([Role::SuperAdmin, Role::Customer]); // returns boolean
```
When multiple roles are passed, the package will only return `true` when the `$user` model has been assigned all roles passed. 

### Has permissions
Models have permissions via roles. Therefore a model only has the permissions that are associated with the roles they have been assigned.

For instance, if a `$user` model has been assigned the `SuperAdmin` and `Admin` roles, the user has all the permissions associated with both roles.
```
// Check if the user has a permission
$user->has(Permission::DeleteProducts); // returns boolean
```

You can decide to check for multiple permissions at once
```
// Check if the user has any of the following permissions.
$user->has(Permission::DeleteProducts, Permission::DeleteTransactions); // returns boolean

// OR as an array
$user->has([Permission::DeleteProducts, Permission::DeleteTransactions]); // returns boolean
```
The `has()` will only return `true` when the `$user` model has all the permissions passed. If the user does not have one of the permissions passed, the method returns `false` . 

>Permissions of different roles can be passed to the `has()` method, the package will check if the user has been assigned the roles associated with the permissions passed, and returns the right boolean.

### Authorize Permissions
For cases where you want to throw an exception when a `model` does not have permission, or multiple permissios, you can use the `authorize()` method to achieve this.

```
$user->authorize(Permission::DeleteTransactions); // Throws a `PermissionDeniedException` if the user does not have this permission

// or authorize miltple permissions
$user->authorize(Permission::DeleteTransactions, Permission::BuyProducts);

// as an array
$user->authorize([Permission::DeleteTransactions, Permission::BuyProducts]);
```
The `authorize()` method returns `true` if the user has the permission(s) passed.

> Permissions of different roles can be passed to the `authorize()` method, the package will check if the user has been assigned the roles associated with the permissions passed, and throw an exception if the `$user` does not have the role.

### Authorize Role
For cases where you want to throw an exception when a `model` does not have a role or multiple roles, you can use the `authorizeRole()` method to achieve this.

```
$user->authorizeRole(Role::SuperAdmin); // throws a `PermissionDeniedException` exception if the user is not a super admin

// Or authorize multiple roles
$user->authorizeRole(Role::SuperAdmin, Role::SuperAdmin); // throws a `PermissionDeniedException` exception if the user is not a super admin
```
The `authorizeRole()` method returns true if the `$user` model has the provided roles.

### Remove roles
```
// remove all user roles
$user->removeRoles(); // returns boolean
```

You can choose specify the role(s) you want to remove
```
// a role can be removed from a user
$user->removeRoles(Role::SuperAdmin); // returns boolean

// or remove multiple roles
$user->removeRoles(Role::SuperAdmin, Role::Admin); // returns boolean

// or as an array
$user->removeRoles([Role::SuperAdmin, Role::Admin]); // returns boolean
```


## Pivot table usage (Many to Many relationship)
Using the roles and permissions package on a pivot table is slightly different.

### Prerequisites 
The following are required for the package to work correctly on a pivot table
- The `pivot_table` MUST have a `role` column (you can change the column name in the config file)
- A `belongsToMany` relationship for the pivot table must exists on one of the models. 

> For better understanding on how `many to many` relationship work, or `pivot_tables` visit [laravel many to many relationship](https://laravel.com/docs/8.x/eloquent-relationships#many-to-many)

### Example
To better explain the implementation, let's assume we are building an application, and in this application, a user can be a member of many merchants, i.e a merchant can have many users.

In this case, we assume we have a `merchant_user` pivot table used as an intermediate table to link users and merchants, and a `role` column to store the user's role in a merchant. 

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
> For the package to work fine with a pivot table, your pivot table **MUST** have a `role` column. 
Alternatively, you can use any name of your choice, but ensure you set the new name in the `config/roles-and-permissions.php` file, under `pivot > column_name`

From the above database structure, the content of the `app\Models\User.php` should look like the following:

`app\Models\User.php`
```
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tarzancodes\RolesAndPermissions\HasRoles;

class User extends Authenticatable 
{  
    use HasRoles;  
    // ...  
    
	public function merchants()
	{
		return  $this->belongsToMany(Merchant::class);
	}
	// ...  
}
```
By importing the `Tarzancodes\RolesAndPermissions\HasRoles` trait and having the `BelongsToMany` relationship, a user can now be assigned roles on a merchant, and perform permissions associated with the roles they are assigned.

After doing the above, the package provides a `of()` method that will be used to perform roles and permissions related logic on the pivot record.

The `of()` method returns an instance of `Tarzancodes\RolesAndPermissions\Repositories\PivotTableRepository` class, which has the same methods used in the [Basic usage section](https://blah.com)   

`assign()`, `has()`, `can()`, `hasRole()`, `authorize()`, `authorizeRole()`, and `removeRoles()` 

### Assigning roles

```
// sample merchant
$merchant = Merchant::where('name', 'wallmart')->first();

// Give the user a super admin role
$user->of($merchant)->assign(Role::SuperAdmin); // returns boolean
```

From the sample above, the `$user` has been assigned a `super admin` role at wallmart. To view the user's permission at wallmart use the code below:

```
// sample merchant
$merchant = Merchant::where('name', 'wallmart')->first();

$user->of($merchant)->permissions(); // returns an array of the user's permissions at wallmart
```

> Note: From the example above, `$user->permissions()` will return an empty array, as the user has not be given any direct permissions. `$user->of($anotherMerchant)->permissions()`  (where `$anotherMerchant` is not wallmart) will return an empty array as the user has not been assigned a role at `$anotherMerchant`

**Multiple roles can also be assigned**
```
// give the user multiple roles on this merchant
$user->of($merchant)->assign(Role::SuperAdmin, Role::Admin); // returns boolean

//or as an array
$user->of($merchant)->assign([Role::SuperAdmin, Role::Admin]);
```
