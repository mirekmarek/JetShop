<?php
namespace JetApplication;

use JetShop\Core_Invoice_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Invoices',
	description: '',
	module_name_prefix: ''
)]
interface Invoice_Manager extends Core_Invoice_Manager {

}