<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_ContentArticleAuthors;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Content - article authors',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ContentArticleAuthors extends Core_Admin_Managers_ContentArticleAuthors
{
}