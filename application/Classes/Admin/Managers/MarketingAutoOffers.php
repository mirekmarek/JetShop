<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_MarketingAutoOffers;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - automatic offers',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_MarketingAutoOffers extends Core_Admin_Managers_MarketingAutoOffers
{
}