<?php
namespace JetApplication;

use JetShop\Core_DeliveryTerm_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Delivery term',
	description: '',
	module_name_prefix: ''
)]
interface DeliveryTerm_Manager extends Core_DeliveryTerm_Manager
{

}