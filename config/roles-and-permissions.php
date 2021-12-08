<?php

return [

    'pivot' => [

        // Configure the name for the column that will be used to store role in the pivot table.
        'role_column_name' => 'role',

        // Table names of existing pivot tables that will require roles and permissions
        // When installing the package, the package will add a `role` column or whatever you have
        // configured the `role_column_name` above to the pivot tables, so you don't need to add it yourself.
        'table_names' => [
            // 'merchant_user',
        ]

    ],

    'roles_enum' => [
        'users' => \App\Enums\Role::class,

        // 'merchant_user' => \App\Enums\MerchantUserRole::class,
    ],

];
