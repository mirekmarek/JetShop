<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Supplier;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Suppliers',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Supplier extends Core_Admin_Managers_Supplier
{

}