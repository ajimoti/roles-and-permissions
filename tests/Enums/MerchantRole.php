<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Enums;

use Tarzancodes\RolesAndPermissions\Helpers\BaseRole;

final class MerchantRole extends BaseRole
{
    // These are sample roles, replace these roles with what works for your application
    public const Distributor = 1;
    public const RetailManager = 2;
    public const CustomerAttendant = 3;

    public $usesHierarchy = true;

    public $deletePivotOnRemove = true;

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
}
