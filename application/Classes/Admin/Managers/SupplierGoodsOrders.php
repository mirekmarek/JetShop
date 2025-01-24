<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_SupplierGoodsOrders;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Supplier - Goods Orders',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_SupplierGoodsOrders extends Core_Admin_Managers_SupplierGoodsOrders
{

}