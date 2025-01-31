<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product;
use JetApplication\Product_Box;

trait Core_Product_EShopData_Trait_Boxes {
	
	/**
	 * @var Product_Box[]
	 */
	protected ?array $boxes = null;
	
	/**
	 * @return Product_Box[]
	 */
	public function getBoxes() : array
	{
		if( $this->boxes===null ) {
			
			$id = $this->getId();
			
			if($this->getType()==Product::PRODUCT_TYPE_VARIANT) {
				$id = $this->getVariantMasterProductId();
			}
			
			$this->boxes = Product_Box::getBoxes( $id );
		}
		
		return $this->boxes;
	}
	
	public function getBoxesWeight() : float
	{
		$weight = 0.0;
		
		foreach($this->getBoxes() as $box) {
			$weight += $box->getWeight();
		}
		
		return $weight;
	}
	
	public function getBoxesVolume() : float
	{
		$volume = 0.0;
		
		foreach($this->getBoxes() as $box) {
			$volume += $box->getVolume();
		}
		
		return $volume;
	}
	
}