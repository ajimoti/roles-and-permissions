<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Enums;

use Tarzancodes\RolesAndPermissions\Helpers\BasePermission;

final class MerchantPermission extends BasePermission
{
    // Product permissions
    const MarkAsSoldOut = 'mark_as_sold_out';
    const BulkOrderGoods = 'bulk_order_goods';
    const CommunicateWithManufacturers = 'communicate_with_manufacturers';
    const EditProduct = 'edit_product';
    const CreateProduct = 'create_product';
    const BuyProduct = 'buy_product';
    const SellProduct = 'sell_product';

    // Transactions permissions
    const DeleteTransaction = 'delete_transaction';
    const ViewTransactions = 'view_transactions';
}
