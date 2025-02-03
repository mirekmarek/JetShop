<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Availability;
use JetApplication\DeliveryTerm_Info;
use JetApplication\Manager_MetaInfo;
use JetApplication\Order;
use JetApplication\Product_EShopData;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Delivery term',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_DeliveryTerm_Manager extends Application_Module
{
	
	abstract public function getInfo( Product_EShopData $product, ?Availability $availability=null ) : DeliveryTerm_Info;
	
	abstract public function setupOrder( Order $order ) : void;
}