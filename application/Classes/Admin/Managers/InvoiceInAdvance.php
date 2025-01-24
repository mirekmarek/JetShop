<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_InvoiceInAdvance;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Invoices in advance',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_InvoiceInAdvance extends Core_Admin_Managers_InvoiceInAdvance
{

}