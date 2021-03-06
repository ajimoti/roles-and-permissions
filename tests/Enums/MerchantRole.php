<?php

namespace Ajimoti\RolesAndPermissions\Tests\Enums;

use Ajimoti\RolesAndPermissions\Helpers\BaseRole;

final class MerchantRole extends BaseRole
{
    // These are sample roles, replace these roles with what works for your application
    public const Distributor = 1;
    public const RetailManager = 2;
    public const CustomerAttendant = 3;
    public const Customer = 4;

    protected static $useHierarchy = true;

    protected static $deletePivotOnRemove = true;

    public static $permissionClass = MerchantPermission::class;

    /**
     * Set available roles and their permissions.
     *
     * @return array
     */
    final public static function permissions(): array
    {
        return [
            self::Distributor => [
                // Super Admin permissions should be here
                MerchantPermission::BulkOrderGoods, MerchantPermission::CommunicateWithManufacturers, MerchantPermission::ViewTransactions,
                MerchantPermission::DeleteTransaction,
            ],

            self::RetailManager => [
                // Admin permissions should be here
                MerchantPermission::MarkAsSoldOut,  MerchantPermission::EditProduct, MerchantPermission::CreateProduct,
            ],

            self::CustomerAttendant => [
                // Customer permissions should be here
                MerchantPermission::SellProduct,
            ],

        ];
    }

    /**
     * Set a description for the roles
     *
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::Distributor => 'Distributes goods to customers',
            self::RetailManager => 'Manages products',
            default => parent::getDescription($value),
        };
    }
}
