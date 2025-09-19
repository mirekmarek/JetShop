<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Application_Service_EShop;
use Jet\Application_Service_MetaInfo;
use JetApplication\Order;

/**
 *
 */
#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: false,
	multiple_mode: true,
	name: 'New order postprocessor',
	description: '',
	module_name_prefix: '',
)]
interface Core_Application_Service_EShop_NewOrderPostprocessor
{
	public function processNewOrder( Order $order );
}