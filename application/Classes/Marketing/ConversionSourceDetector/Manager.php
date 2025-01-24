<?php
namespace JetApplication;

use JetShop\Core_Marketing_ConversionSourceDetector_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: false,
	name: 'Conversion source detector',
	description: '',
	module_name_prefix: ''
)]
interface Marketing_ConversionSourceDetector_Manager extends Core_Marketing_ConversionSourceDetector_Manager
{
}