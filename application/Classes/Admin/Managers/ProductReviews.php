<?php

namespace JetApplication;

use JetShop\Core_Admin_Managers_ProductReviews;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Catalog - product reviews',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ProductReviews extends Core_Admin_Managers_ProductReviews
{
}