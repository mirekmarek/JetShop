<?php

namespace JetApplication;

use JetShop\Core_Admin_Managers_Entity_Edit_Simple;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Entity editor - Simple',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Entity_Edit_Simple extends Core_Admin_Managers_Entity_Edit_Simple
{
}