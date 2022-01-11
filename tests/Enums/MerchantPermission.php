<?php

namespace Ajimoti\RolesAndPermissions\Tests\Enums;

use Ajimoti\RolesAndPermissions\Helpers\BasePermission;

final class MerchantPermission extends BasePermission
{
    // Product permissions
    public const MarkAsSoldOut = 'mark_as_sold_out';
    public const BulkOrderGoods = 'bulk_order_goods';
    public const CommunicateWithManufacturers = 'communicate_with_manufacturers';
    public const EditProduct = 'edit_product';
    public const CreateProduct = 'create_product';
    public const BuyProduct = 'buy_product';
    public const SellProduct = 'sell_product';

    // Transactions permissions
    public const DeleteTransaction = 'delete_transaction';
    public const ViewTransactions = 'view_transactions';
}
