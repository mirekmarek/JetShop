<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_EShop;
use JetApplication\Product_EShopData;
use JetApplication\ProductListing;
use Closure;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: true,
	name: 'Product Listing',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_Application_Service_EShop_ProductListing extends Application_Module
{
	abstract public function init( array $product_ids ) : void;
	
	abstract public function handle() : void;
	
	abstract public function render() : string;
	
	abstract public function renderItem( Product_EShopData $item ) : string;
	
	abstract public function getListing(): ProductListing;
	
	abstract public function setOptionalURLParameter( string $optional_URL_parameter ): void;
	
	abstract public function setCategoryId( int $category_id ): void;
	
	abstract public function setAjaxEventHandler( Closure $ajax_event_handler ): void;
	
}