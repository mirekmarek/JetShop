<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_DeliveryNote;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Delivery note',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_DeliveryNote extends Core_Admin_Managers_DeliveryNote
{

}