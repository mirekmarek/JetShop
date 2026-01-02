<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Accessories_Accessory;
use JetApplication\Accessories_Group;
use JetApplication\Product_Accessories_Group;

trait Core_Product_EShopData_Trait_Accessories
{
	
	public function getDirectAccessoriesIds() : array
	{
		$id = $this->getId();
		if($this->isVariant()) {
			$id = $this->getVariantMasterProductId();
		}
		
		
		return Accessories_Accessory::getAccessoryIds( $id );
	}
	
	
	public function getAccessoriesGroupIds() : array
	{
		$id = $this->getId();
		if($this->isVariant()) {
			$id = $this->getVariantMasterProductId();
		}
		
		return Product_Accessories_Group::getGroupIds( $id );
	}

	
	public function getAccessoriesProductIds() : array
	{
		$groups = $this->getAccessoriesGroupIds();
		
		$by_groups = Accessories_Group::getAccessoriesIds( $groups );
		$direct = $this->getDirectAccessoriesIds();
		
		$accessories = array_merge(  $by_groups, $direct );
		array_unique( $accessories );
		
		return $accessories;
	}
}