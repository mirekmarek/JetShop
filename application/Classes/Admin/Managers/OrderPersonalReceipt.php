<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_OrderPersonalReceipt;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Order Personal Receipt',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_OrderPersonalReceipt extends Core_Admin_Managers_OrderPersonalReceipt
{

}