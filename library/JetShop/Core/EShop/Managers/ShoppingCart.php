<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\ShoppingCart;
use JetApplication\Product_EShopData;

interface Core_EShop_Managers_ShoppingCart {
	public function getCart() : ShoppingCart;
	
	public function saveCart() : void;
	
	public function resetCart() : void;
	
	public function renderIntegration() : string;
	
	public function renderIcon() : string;
	
	public function renderBuyButton_listing( Product_EShopData $product ) : string;
	
	public function renderBuyButton_detail( Product_EShopData $product ) : string;
}