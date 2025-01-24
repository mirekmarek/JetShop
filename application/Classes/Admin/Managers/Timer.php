<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Timer;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Timer',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Timer extends Core_Admin_Managers_Timer
{

}