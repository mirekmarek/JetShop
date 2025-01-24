<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_ReturnOfGoods;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Return of goods',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ReturnOfGoods extends Core_Admin_Managers_ReturnOfGoods
{

}