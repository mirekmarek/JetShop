<?php
namespace JetApplication;

use JetShop\Core_NumberSeries_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Number series',
	description: '',
	module_name_prefix: ''
)]
abstract class NumberSeries_Manager extends Core_NumberSeries_Manager
{

}