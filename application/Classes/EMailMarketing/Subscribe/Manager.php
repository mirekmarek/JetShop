<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use JetShop\Core_EMailMarketing_Subscribe_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'E-mail marketing subscribe',
	description: '',
	module_name_prefix: ''
)]
abstract class EMailMarketing_Subscribe_Manager extends Core_EMailMarketing_Subscribe_Manager {

}