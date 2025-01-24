<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_MagicTags;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Magic tags',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_MagicTags extends Core_EShop_Managers_MagicTags
{

}