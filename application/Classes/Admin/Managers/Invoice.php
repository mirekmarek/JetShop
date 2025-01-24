<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Invoice;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Invoices',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Invoice extends Core_Admin_Managers_Invoice
{

}