<?php

namespace JetApplication;

use JetShop\Core_Admin_Managers_ProductQuestions;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Catalog - product questions',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ProductQuestions extends Core_Admin_Managers_ProductQuestions
{
}