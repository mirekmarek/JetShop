<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\Product_EShopData;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Product reviews',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_ProductReviews extends Application_Module
{
	
	abstract public function renderRank( Product_EShopData $product ) : string;
	abstract public function renderReviews( Product_EShopData $product ): string;
	abstract public function handleCustomerSectionReviews() : void;
	abstract public function renderCustomerSectionReviews() : string;
	
}