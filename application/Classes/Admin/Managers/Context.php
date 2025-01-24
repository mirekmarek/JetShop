<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Context;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Context',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Context extends Core_Admin_Managers_Context
{

}