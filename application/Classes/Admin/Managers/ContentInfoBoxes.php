<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_ContentInfoBoxes;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Content - Info boxes',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ContentInfoBoxes extends Core_Admin_Managers_ContentInfoBoxes
{
}