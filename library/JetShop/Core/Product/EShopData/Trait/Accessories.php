<?php
namespace JetShop;

use JetApplication\Accessories_Group;
use JetApplication\Product_Accessories_Group;

trait Core_Product_EShopData_Trait_Accessories
{
	
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
		
		return Accessories_Group::getAccessoriesIds( $groups );
	}
}