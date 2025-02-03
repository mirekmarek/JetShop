<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\EShopEntity_HasTimer_Interface;
use JetApplication\Manager_MetaInfo;


#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Timer',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_Timer extends Application_Module
{
	abstract public function renderIntegration() : string;
	
	abstract public function renderEntityEdit( EShopEntity_HasTimer_Interface $entity, bool $editable ) : string;

}