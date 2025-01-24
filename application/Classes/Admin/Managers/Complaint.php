<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Complaint;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Complaints',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Complaint extends Core_Admin_Managers_Complaint
{
}