<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Customer;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Customers',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Customer extends Core_Admin_Managers_Customer
{

}