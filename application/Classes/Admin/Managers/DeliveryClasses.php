<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_DeliveryClasses;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Delivery classes',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_DeliveryClasses extends Core_Admin_Managers_DeliveryClasses
{

}