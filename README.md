
# Laravel roles and permissions


## Introduction
This package allows you to assign roles and permissions to any laravel model, or on a pivot table (`many to many relationship`).

## Quick Samples

Below are samples of how to use the pacakge after installation. 

### Basic Usage (On any model)
The example below is done on a `User` model, but will also work on any model class.

**First step:** 
Import the `Tarzancodes\RolesAndPermissions\Tests\Enums\Role` and `Tarzancodes\RolesAndPermissions\Tests\Enums\Permission` class.
```
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;
```

Then following can be done:
```
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

**First step:** 
Import the `Tarzancodes\RolesAndPermissions\Tests\Enums\Role` and `Tarzancodes\RolesAndPermissions\Tests\Enums\Permission` class.
```
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;
```

```
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

### Sample
---
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

> When the `of()` method is chained to a model, the package will automatically use Laravel relationship naming convention to guess the relationship name. Alternatively you can pass the relationship name as the second argument.


For the explanations below, we'd assume the variable `$merchant` is set to a merchant with name `wallmart` like so:
```
$merchant = Merchant::where('name', 'wallmart')->first();
```


### Assigning roles
---
```
// Give the user a super admin role
$user->of($merchant)->assign(Role::SuperAdmin); // returns boolean
```

From the sample above, the `$user` has been assigned a `super admin` role at wallmart. To view the user's permission at wallmart use the code below:

```
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
From the sample above, the `$user` has been assigned a `super admin` and `admin` role at wallmart.

### Get roles

```
// Get ther user roles at the selected merchant (wallmart)
$user->of($merchant)->roles(); // returns an array of the user's roles

```

### Get Permissions
```
// Get ther user permissions at the selected merchant (wallmart)
$user->of($merchant)->permissions(); // returns an array of the user's permissions at 

```
### Has Role
Check if a user has a specific role, or multiple roles

```
$user->of($merchant)->hasRole(Role::SuperAdmin); // returns boolean
```

Or check for multiple roles
```
// check if user has the provided roles at the selected merchant 
$user->of($merchant)->hasRole(Role::SuperAdmin, Role::Customer); // returns boolean

$user->of($merchant)->hasRole([Role::SuperAdmin, Role::Customer]); // returns boolean

```

When multiple roles are passed, the package will only return  `true`  when the  `$user`  model of the `$merchant` has been assigned all roles passed.


### Has permissions

Pivot records have permissions via roles. Therefore a record only has permissions that are associated with the roles they have been assigned.

For instance, if a  `$user`  model has been assigned the  `SuperAdmin`  and  `Admin`  roles at a `merchant` , the user has all the permissions associated with both roles ONLY at this merchant. Calling the `has()` method directly on the `$user` model will return false.

```
// Check if the user has a permission
$user->of($merchant)->has(Permission::DeleteProducts); // returns boolean

```

You can decide to check for multiple permissions at once

```
// Check if the user has any of the following permissions.
$user->of($merchant)->has(Permission::DeleteProducts, Permission::DeleteTransactions); // returns boolean

// OR as an array
$user->of($merchant)->has([Permission::DeleteProducts, Permission::DeleteTransactions]); // returns boolean

```

The  `has()`  will only return  `true`  when the  `$user`  model has all the permissions passed at the `merchant`. If the user does not have one or more of the permissions passed, the method returns  `false`  .

> Permissions of different roles can be passed to the  `has()`  method, the package will check if the user has been assigned the roles associated with the permissions passed, and returns the right boolean.

### Authorize Permissions
For cases where you want to throw an exception when a  `pivot record`  does not have permission, or multiple permissions, you can use the  `authorize()`  method to achieve this.

```
$user->of($merchant)->authorize(Permission::DeleteTransactions); // Throws a `PermissionDeniedException` if the user does not have this permission at the selected merchant
```
```
// or authorize miltple permissions
$user->of($merchant)->authorize(Permission::DeleteTransactions, Permission::BuyProducts);
```
```
// as an array
$user->of($merchant)->authorize([Permission::DeleteTransactions, Permission::BuyProducts]);
```

The  `authorize()`  method returns  `true`  if the user has the permission(s) passed at the `merchant`.

> Permissions of different roles can be passed to the  `authorize()`  method, the package will check if the user has been assigned the roles associated with the permissions passed, and throw an exception if the  `$user`  does not have the role

### Authorize Role
For cases where you want to throw an exception when a  `pivot record`  does not have a role or multiple roles, you can use the  `authorizeRole()`  method to achieve this.

```
$user->of($merchant)->authorizeRole(Role::SuperAdmin); // throws a `PermissionDeniedException` exception if the user is not a super admin at the selected merchant
```

```
// Or authorize multiple roles
$user->of($merchant)->authorizeRole(Role::SuperAdmin, Role::SuperAdmin); // throws a `PermissionDeniedException` exception if the user is not a super admin at the selected merchant
```

The  `authorizeRole()`  method returns true if the  `$user`  model has the provided roles at the selected `merchant`.

### Remove roles
```
// remove all pivot record roles at
$user->of($merchant)->removeRoles(); // returns boolean

```

You can choose to specify the role(s) you want to remove

```
// a role can be removed from a user of the selected merchant
$user->of($merchant)->removeRoles(Role::SuperAdmin); // returns boolean

// or remove multiple roles
$user->of($merchant)->removeRoles(Role::SuperAdmin, Role::Admin); // returns boolean

// or as an array
$user->of($merchant)->removeRoles([Role::SuperAdmin, Role::Admin]); // returns boolean

```

>Note: Provided the model extends Laravel `
Illuminate\Foundation\Auth\User
`, these methods are also available to the authenticated user via the `Auth` facade's `user` method. i.e `auth()->user()->of($merchant)` will also return an instance of `Tarzancodes\RolesAndPermissions\Repositories\PivotTableRepository`

## Working with other columns on the pivot table
There are cases where you will have extra columns on your pivot table, and have to set values for the columns while assigning roles, or check for permissions based on the value of a column. This section explains how to go about this.

### Sample case
Using the same `merchants` and `users`  example in the [Pivot table usage](https:://blah.com) section above, but in this case, the following rules applies:

- Each merchant has `departments`
- Users can belong to different `departments`
- Users have different roles in different `departments`

Below is what our database structure will look like:
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
    department - string

```

### Assign roles and set pivot column values

When assigning roles, you can use the `withPivot()` method to set values for any other column on the pivot table.

**For example:** 
If we decide to assign a `role`  to the `$user` of a `$merchant`'s `department` , below is an example of what the code will look like. 
```
// Give the user a super admin role in the 'product' department of this merchant
$user->of($merchant)
	->withPivot(['department' => 'product'])
	->assign(Role::SuperAdmin); // returns boolean

```
From the sample above, the  `$user` model has been assigned a  `super admin`  role at `product`  department of the provided `$merchant`.  In the background a record is created in the `merchant_user` pivot table, with the columns set to the correct values.

You can decide to set multiple pivot columns by either chaining multiple `withPivot()` or set all values in the an array.

```
$user->of($merchant)
	->withPivot(['department' => 'product'])
	->withPivot(['created_at' => now()])
	->assign(Role::SuperAdmin); // returns boolean
	
//OR
$user->of($merchant)
	->withPivot([
		'department' => 'product',
		'created_at' => now()
	])
	->assign(Role::SuperAdmin); // returns boolean
```

### Roles and Permissions with conditions
If you would like to conditionally select a pivot record before checking for the roles and permissions on it, you achieve this by chaining any of the `belongsToMany` relationship query method to the `of()` method. 

**Allowed methods**
`wherePivot`, `wherePivotIn`, `wherePivotNotIn`, `wherePivotBetween`, `wherePivotNotBetween`, `wherePivotNull`, and `wherePivotNotNull`

The package supports every method listed in the section of laravel's documentation: [Filtering Queries Via Intermediate Table Columns](https://laravel.com/docs/8.x/eloquent-relationships#filtering-queries-via-intermediate-table-columns)

**Examples**
If you decide to check if the user has a role in a merchant's department, below is an example of what the code will look like:

```
$user->of($merchant)
	->wherePivot('department', 'product')	
	->hasRole(Role::SuperAdmin); // returns boolean
```
The above code will only return `true` if the user has been previously assigned a `super admin` role in the `product` department of the merchant provided. Otherwise the code returns false, even if the user has a `super admin` role in another department.

You can decide to chain as many filter method as possible, e.g:

```
$user->of($merchant)
	->wherePivot('department', 'product')	
	->wherePivotBetween('created_at', ['2021-12-05 00:00:00', '2021-12-08 00:00:00'])
	->wherePivotNull('updated_at')
	->has(Permission::BuyProducts); // returns boolean
```

# Exceptions
#### Invalid Relation Name Exception
This exception is only applies when using the package on a pivot table.

>An instance of `Tarzancodes\RolesAndPermissions\Exceptions\InvalidRelationNameExceptionException` is thrown whenever the package can not resolve the `belongsToMany` relationship name

When the `of()` method is chained to a model, the package will automatically use Laravel relationship naming convention to guess the relationship name. If the package cannot find a method with the relationship name in your model class,  this exception is thrown.

To fix this, ensure you are following Laravel's naming convention for your relationship names, alternatively you can pass the relationship name as the second argument.

**For example:**
Instead of following Laravel's naming convention, we declare the `merchants` relationship on the user model like below: 
`app\Models\User.php`

```
    // ...  
    public function userMerchants()
    {
        return  $this->belongsToMany(Merchant::class);
    }
    // ...  
```

Runing `$user->of($merchant)->assign(Role::SuperAdmin)` will throw the `InvalidRelationNameExceptionException` exception, as the will end up looking for a `merchants()` method instead of `userMerchants()`.

To fix this, you can pass your relationship name as the second argument of the `of()` method. So we have something like below

```
$user->of($merchant, 'userMerchants')->assign(Role::SuperAdmin);
```

### Permission Denied Exception
An instance of `Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException` is thrown when a provided `roles` or `permissions` are not assigned to the model.

You will only experience these exceptions when using the `authorize()` or `authorizeRole()` method. If you do not want exceptions to be thrown, you should use the any of the other methods as they only return booleans e.g. `can()`, `has()`, `hasRole()`


### Invalid Role Hierarchy Exception
An instance of `Tarzancodes\RolesAndPermissions\Exceptions\InvalidRoleHierarchyException` is thrown when the `$useHierarchy` static property of a role enum class is set to true, and the roles in the `permissions()` method do NOT appear in the same order that they are declared as constants.

When setting the roles to be in hierarchy, it is important that the roles constants are arranged in the same order that they were declared in the `permissions()` array. If they are not arranged in the same order they were declared, the `InvalidRoleHierarchyException` is thrown.

# Advance Usage
