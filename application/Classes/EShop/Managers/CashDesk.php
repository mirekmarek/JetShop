<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_CashDesk;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Cash Desk',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_CashDesk extends Core_EShop_Managers_CashDesk
{

}