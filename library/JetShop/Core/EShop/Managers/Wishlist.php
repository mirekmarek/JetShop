<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_EShopData;

interface Core_EShop_Managers_Wishlist {
	
	public function renderIntegration() : string;
	
	public function renderProductButton( Product_EShopData $product, bool $container=true ) : string;
	
	public function renderIcon() : string;
	
}