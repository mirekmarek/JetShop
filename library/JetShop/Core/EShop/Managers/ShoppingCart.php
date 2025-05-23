<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\ShoppingCart;
use JetApplication\Product_EShopData;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Shopping Cart',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_ShoppingCart extends Application_Module
{
	abstract public function getCart() : ShoppingCart;
	
	abstract public function saveCart() : void;
	
	abstract public function resetCart() : void;
	
	abstract public function renderIntegration() : string;
	
	abstract public function renderIcon() : string;
	
	abstract public function renderBuyButton_listing( Product_EShopData $product ) : string;
	
	abstract public function renderBuyButton_detail( Product_EShopData $product ) : string;
}