<?php
namespace JetShop;

interface Core_EShop_Managers_PromoAreas {

	public function renderArea( string $area_code, array $list_of_product_ids_to_check_relevance=[] ) : string;

}