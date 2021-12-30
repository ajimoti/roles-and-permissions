<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Enums;

use Tarzancodes\RolesAndPermissions\Helpers\BasePermission;

final class Permission extends BasePermission
{
    // Product permissions
    public const DeleteProduct = 'delete_product';
    public const MarkAsSoldOut = 'mark_as_sold_out';
    public const EditProduct = 'edit_product';
    public const CreateProduct = 'create_product';
    public const BuyProduct = 'buy_product';

    // Transactions permissions
    public const DeleteTransaction = 'delete_transaction';
    public const ViewTransaction = 'view_transaction';
}
