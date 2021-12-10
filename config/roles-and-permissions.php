<?php

return [

    'pivot' => [

        // Configure the name for the column that will be used to store role in the pivot table.
        'column_name' => 'role',

        // Table names of existing pivot tables that will require roles and permissions
        // When installing the package, the package will add a `role` column or whatever you have
        // configured the `column_name` above to the pivot tables, so you don't need to add it yourself.
        'tables' => [
            // 'merchant_user',
        ]

    ],

    // Configure the 'role' enum to use for each table.
    // By default, the package will use the default enum set above.

    'roles_enum' => [
        'default' => \App\Enums\Role::class,

        // For pivot tables, write the table name as the key and the enum class as the value.
        // for a normal model class, write the table name of the model as the key and the enum class as the value.

        // 'merchant_user' => \App\Enums\MerchantUserRole::class,
    ],

];
