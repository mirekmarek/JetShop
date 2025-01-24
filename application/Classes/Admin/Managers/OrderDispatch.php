<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_OrderDispatch;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Order dispatch',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_OrderDispatch extends Core_Admin_Managers_OrderDispatch
{

}