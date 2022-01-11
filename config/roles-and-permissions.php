<?php

return [

    /**
     * The name of the "role" column on the pivot table.
     *
     * The name set here will also be used on the `model_role` table.
     */
    'column_name' => 'role',

    'pivot' => [
        /**
         * Table names of existing pivot tables that will require roles and permissions
         *
         * This config value is only used when installing the package.
         *
         * When installing the package, the package will add a `role` column (or whatever value you have
         * configured the `column_name` above) to the pivot tables listed to the pivot tables,
         * so you do not have to bother about adding .
         */
        'tables' => [
            // 'merchant_user', 'department_user', ...
        ]

    ],

    /**
     * Configure the 'role' enum to use for each table.
     *
     * The key of the array should be the table name, and the value is the enum class name.
     * The enum class name should be a class that extends the `Ajimoti\RolesAndPermissions\Enums\Role` class.
     */
    'roles_enum' => [
        /**
         * The default role enum to use for each table.
         *
         * This is the default enum that will be used if no enum is set for a table.
         */
        'default' => \App\Enums\Role::class,

        // Examples:
        // 'merchant_user' => \App\Enums\MerchantUserRole::class,
    ],

];
