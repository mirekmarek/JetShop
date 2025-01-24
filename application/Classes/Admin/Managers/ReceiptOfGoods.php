<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_ReceiptOfGoods;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Receipt of goods',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ReceiptOfGoods extends Core_Admin_Managers_ReceiptOfGoods
{

}