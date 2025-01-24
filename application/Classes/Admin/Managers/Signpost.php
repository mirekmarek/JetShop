<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Signpost;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - Signposts',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Signpost extends Core_Admin_Managers_Signpost
{

}