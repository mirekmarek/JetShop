<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_ContentArticles;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Content - articles',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ContentArticles extends Core_Admin_Managers_ContentArticles
{
}