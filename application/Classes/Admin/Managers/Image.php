<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Image;


#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Images',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Image extends Core_Admin_Managers_Image
{
}