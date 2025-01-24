<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_ProductQuestions;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Product questions',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_ProductQuestions extends Core_EShop_Managers_ProductQuestions
{

}