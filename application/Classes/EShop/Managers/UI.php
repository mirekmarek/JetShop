<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_UI;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'UI',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_UI extends Core_EShop_Managers_UI
{

}