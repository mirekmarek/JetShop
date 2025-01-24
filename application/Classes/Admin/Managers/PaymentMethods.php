<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_PaymentMethods;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Payment methods',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_PaymentMethods extends Core_Admin_Managers_PaymentMethods
{

}