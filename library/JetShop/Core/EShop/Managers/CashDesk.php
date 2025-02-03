<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\CashDesk;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Cash Desk',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_CashDesk extends Application_Module
{
	
	abstract public function getCashDesk() : CashDesk;
	
}