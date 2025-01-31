<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_EShopData;

interface Core_EShop_Managers_ProductListing
{
	public function init( array $product_ids, int $category_id=0, string $category_name='', ?string $optional_URL_parameter = null ) : void;
	
	public function render() : string;
	
	public function renderItem( Product_EShopData $item ) : string;
}