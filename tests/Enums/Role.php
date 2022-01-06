<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Enums;

use Tarzancodes\RolesAndPermissions\Helpers\BaseRole;

final class Role extends BaseRole
{
    public const SuperAdmin = 'super_admin';
    public const Admin = 'admin';
    public const Customer = 'customer';


    /**
     * The permission enum class used by this role.
     *
     * @var BasePermission
     */
    protected static $permissionClass = Permission::class;

    /**
     * Set available roles and their permissions.
     *
     * @return array
     */
    final public static function permissions(): array
    {
        return [
            self::SuperAdmin => [
                // Super Admin permissions should be here
                Permission::DeleteProduct, Permission::DeleteTransaction, Permission::ViewTransaction,
            ],

            self::Admin => [
                // Admin permissions should be here
                Permission::MarkAsSoldOut,  Permission::EditProduct, Permission::CreateProduct,
            ],

            self::Customer => [
                // Customer permissions should be here
                Permission::BuyProduct,
            ],
        ];
    }
}
