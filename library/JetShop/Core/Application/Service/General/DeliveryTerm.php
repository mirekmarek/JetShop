<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_General;
use JetApplication\Availability;
use JetApplication\DeliveryTerm_Info;
use Jet\Application_Service_MetaInfo;
use JetApplication\Order;
use JetApplication\Product_EShopData;

#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: true,
	name: 'Delivery term',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_General_DeliveryTerm extends Application_Module
{
	
	abstract public function getInfo( Product_EShopData $product, float $units_required=1, ?Availability $availability=null ) : DeliveryTerm_Info;
	
	abstract public function setupOrder( Order $order ) : void;
}