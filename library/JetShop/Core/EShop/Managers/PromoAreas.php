<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


interface Core_EShop_Managers_PromoAreas {

	public function renderArea( string $area_code, array $list_of_product_ids_to_check_relevance=[] ) : string;

}