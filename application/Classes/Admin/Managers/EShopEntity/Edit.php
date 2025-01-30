<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_EShopEntity_Edit;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Entity editor',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_EShopEntity_Edit extends Core_Admin_Managers_EShopEntity_Edit
{
}