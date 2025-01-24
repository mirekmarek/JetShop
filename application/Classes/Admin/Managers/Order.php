<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Order;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Orders',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Order extends Core_Admin_Managers_Order
{

}