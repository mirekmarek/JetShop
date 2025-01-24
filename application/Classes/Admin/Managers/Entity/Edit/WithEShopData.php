<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Entity_Edit_WithEShopData;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Entity editor - With e-shop data',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Entity_Edit_WithEShopData extends Core_Admin_Managers_Entity_Edit_WithEShopData
{
}