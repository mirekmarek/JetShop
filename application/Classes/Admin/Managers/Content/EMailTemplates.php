<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Content_EMailTemplates;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Content - e-mail templates',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Content_EMailTemplates extends Core_Admin_Managers_Content_EMailTemplates
{
}