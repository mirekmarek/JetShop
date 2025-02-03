<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Promo areas',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_PromoAreas extends Application_Module
{
	abstract public function renderArea( string $area_code, array $list_of_product_ids_to_check_relevance=[] ) : string;
}