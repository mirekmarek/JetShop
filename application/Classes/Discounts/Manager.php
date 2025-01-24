<?php
namespace JetApplication;

use JetShop\Core_Discounts_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Discounts',
	description: '',
	module_name_prefix: ''
)]
abstract class Discounts_Manager extends Core_Discounts_Manager
{
}