<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_FulltextSearch;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Fulltext Search',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_FulltextSearch extends Core_Admin_Managers_FulltextSearch
{

}