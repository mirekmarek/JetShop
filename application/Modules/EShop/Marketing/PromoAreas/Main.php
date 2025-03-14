<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Marketing\PromoAreas;

use JetApplication\Marketing_PromoArea;
use JetApplication\Marketing_PromoAreaDefinition;
use JetApplication\EShop_Managers_PromoAreas;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;

class Main extends EShop_Managers_PromoAreas implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected ?array $areas_map = null;
	protected ?array $active_areas = null;
	
	protected function getAreasMap() : array
	{
		if($this->areas_map===null) {
			$this->areas_map = Marketing_PromoAreaDefinition::getActiveMap();
		}
		
		return $this->areas_map;
	}
	
	public function renderArea( string $area_code, array $list_of_product_ids_to_check_relevance = [] ): string
	{
		$map = $this->getAreasMap();
		$area_id = $map[$area_code]??0;
		if(!$area_id) {
			return '';
		}
		
		if($this->active_areas===null) {
			$this->active_areas = Marketing_PromoArea::getAllActive();
		}
		
		$areas = [];
		foreach($this->active_areas as $area) {
			if(
				$area->getPromoAreaId()==$area_id &&
				(
					!$list_of_product_ids_to_check_relevance ||
					$area->isRelevant( $list_of_product_ids_to_check_relevance )
				)
			) {
				$areas[] = $area;
			}
		}
		
		$res = '';
		
		foreach($areas as $area) {
			$view = $this->getView();
			$view->setVar('area_code', $area_code );
			$view->setVar('area', $area );
			
			$res .= $view->render('area');
		}
		
		return $res;
	}
}