<?php
namespace JetApplication;

use JetShop\Core_Calendar_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Calendar',
	description: '',
	module_name_prefix: ''
)]
interface Calendar_Manager extends Core_Calendar_Manager {

}
