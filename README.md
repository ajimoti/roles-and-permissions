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

// Assign a 'Super Admin' role to this user on the merchant (wallmart)
$user->of($merchant)->assign(Role::SuperAdmin);

// Check if the user has a super admin role on the merchant (wallmart)
$user->of($merchant)->hasRole(Role::SuperAdmin); 

// Check if the user can 'delete transactions' on the merchant (wallmart)
$user->of($merchant)->can(Permission::DeleteTransactions);

// Check if the user has multiple permissions on the merchant (wallmart)
$user->of($merchant)->has(Permission::DeleteTransactions, Permission::BlockUsers);

// Authorize permissions
// Throws an exception if the user does not have these permissions on the merchant (wallmart)
$user->of($merchant)->authorize(Permission::DeleteTransactions, Permission::BlockUsers);

// Authorize role
// Throws an exception if the user does not have the permissions on the merchant (wallmart)
$user->authorizeRole(Role::SuperAdmin);
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

## Create files and folders

The file explorer is accessible using the button in left corner of the navigation bar. You can create a new file by clicking the **New file** button in the file explorer. You can also create folders by clicking the **New folder** button.

## Switch to another file

All your files and folders are presented as a tree in the file explorer. You can switch from one to another by clicking a file in the tree.

## Rename a file

You can rename the current file by clicking the file name in the navigation bar or by clicking the **Rename** button in the file explorer.

## Delete a file

You can delete the current file by clicking the **Remove** button in the file explorer. The file will be moved into the **Trash** folder and automatically deleted after 7 days of inactivity.

## Export a file

You can export the current file by clicking **Export to disk** in the menu. You can choose to export the file as plain Markdown, as HTML using a Handlebars template or as a PDF.


# Synchronization

Synchronization is one of the biggest features of StackEdit. It enables you to synchronize any file in your workspace with other files stored in your **Google Drive**, your **Dropbox** and your **GitHub** accounts. This allows you to keep writing on other devices, collaborate with people you share the file with, integrate easily into your workflow... The synchronization mechanism takes place every minute in the background, downloading, merging, and uploading file modifications.

There are two types of synchronization and they can complement each other:

- The workspace synchronization will sync all your files, folders and settings automatically. This will allow you to fetch your workspace on any other device.
	> To start syncing your workspace, just sign in with Google in the menu.

- The file synchronization will keep one file of the workspace synced with one or multiple files in **Google Drive**, **Dropbox** or **GitHub**.
	> Before starting to sync files, you must link an account in the **Synchronize** sub-menu.

## Open a file

You can open a file from **Google Drive**, **Dropbox** or **GitHub** by opening the **Synchronize** sub-menu and clicking **Open from**. Once opened in the workspace, any modification in the file will be automatically synced.

## Save a file

You can save any file of the workspace to **Google Drive**, **Dropbox** or **GitHub** by opening the **Synchronize** sub-menu and clicking **Save on**. Even if a file in the workspace is already synced, you can save it to another location. StackEdit can sync one file with multiple locations and accounts.

## Synchronize a file

Once your file is linked to a synchronized location, StackEdit will periodically synchronize it by downloading/uploading any modification. A merge will be performed if necessary and conflicts will be resolved.

If you just have modified your file and you want to force syncing, click the **Synchronize now** button in the navigation bar.

> **Note:** The **Synchronize now** button is disabled if you have no file to synchronize.

## Manage file synchronization

Since one file can be synced with multiple locations, you can list and manage synchronized locations by clicking **File synchronization** in the **Synchronize** sub-menu. This allows you to list and remove synchronized locations that are linked to your file.


# Publication

Publishing in StackEdit makes it simple for you to publish online your files. Once you're happy with a file, you can publish it to different hosting platforms like **Blogger**, **Dropbox**, **Gist**, **GitHub**, **Google Drive**, **WordPress** and **Zendesk**. With [Handlebars templates](http://handlebarsjs.com/), you have full control over what you export.

> Before starting to publish, you must link an account in the **Publish** sub-menu.

## Publish a File

You can publish your file by opening the **Publish** sub-menu and by clicking **Publish to**. For some locations, you can choose between the following formats:

- Markdown: publish the Markdown text on a website that can interpret it (**GitHub** for instance),
- HTML: publish the file converted to HTML via a Handlebars template (on a blog for example).

## Update a publication

After publishing, StackEdit keeps your file linked to that publication which makes it easy for you to re-publish it. Once you have modified your file and you want to update your publication, click on the **Publish now** button in the navigation bar.

> **Note:** The **Publish now** button is disabled if your file has not been published yet.

## Manage file publication

Since one file can be published to multiple locations, you can list and manage publish locations by clicking **File publication** in the **Publish** sub-menu. This allows you to list and remove publication locations that are linked to your file.


# Markdown extensions

StackEdit extends the standard Markdown syntax by adding extra **Markdown extensions**, providing you with some nice features.

> **ProTip:** You can disable any **Markdown extension** in the **File properties** dialog.


## SmartyPants

SmartyPants converts ASCII punctuation characters into "smart" typographic punctuation HTML entities. For example:

|                |ASCII                          |HTML                         |
|----------------|-------------------------------|-----------------------------|
|Single backticks|`'Isn't this fun?'`            |'Isn't this fun?'            |
|Quotes          |`"Isn't this fun?"`            |"Isn't this fun?"            |
|Dashes          |`-- is en-dash, --- is em-dash`|-- is en-dash, --- is em-dash|


## KaTeX

You can render LaTeX mathematical expressions using [KaTeX](https://khan.github.io/KaTeX/):

The *Gamma function* satisfying $\Gamma(n) = (n-1)!\quad\forall n\in\mathbb N$ is via the Euler integral

$$
\Gamma(z) = \int_0^\infty t^{z-1}e^{-t}dt\,.
$$

> You can find more information about **LaTeX** mathematical expressions [here](http://meta.math.stackexchange.com/questions/5020/mathjax-basic-tutorial-and-quick-reference).


## UML diagrams

You can render UML diagrams using [Mermaid](https://mermaidjs.github.io/). For example, this will produce a sequence diagram:

```mermaid
sequenceDiagram
Alice ->> Bob: Hello Bob, how are you?
Bob-->>John: How about you John?
Bob--x Alice: I am good thanks!
Bob-x John: I am good thanks!
Note right of John: Bob thinks a long<br/>long time, so long<br/>that the text does<br/>not fit on a row.

Bob-->Alice: Checking with John...
Alice->John: Yes... John, how are you?
```

And this will produce a flow chart:

```mermaid
graph LR
A[Square Rect] -- Link text --> B((Circle))
A --> C(Round Rect)
B --> D{Rhombus}
C --> D
```
