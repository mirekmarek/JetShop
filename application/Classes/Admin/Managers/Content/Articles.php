<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Content_Articles;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Content - articles',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Content_Articles extends Core_Admin_Managers_Content_Articles
{
}