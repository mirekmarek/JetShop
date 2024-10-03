<?php
namespace JetShop;

use JetApplication\Product;
use JetApplication\Product_Box;

trait Core_Product_Trait_Boxes
{
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
			if($this->getType()==Product::PRODUCT_TYPE_SET) {
				$this->boxes = [];
				foreach( $this->getSetItems() as $set_item ) {
					$set_item = Product::load( $set_item->getItemProductId() );
					foreach($set_item->getBoxes() as $box) {
						$this->boxes[] = $box;
					}
				}
				
				return $this->boxes;
			}
			
			$id = $this->id;
			
			if($this->getType()==Product::PRODUCT_TYPE_VARIANT) {
				$id = $this->getVariantMasterProductId();
			}
			
			$this->boxes = Product_Box::getBoxes( $id );
		}
		
		return $this->boxes;
	}
	
	
	public function getBox( int $id ) : ?Product_Box
	{
		$this->getBoxes();
		return $this->boxes[$id]??null;
	}
	
	public function deleteBox( int $id ) : void
	{
		
		$box = $this->getBox( $id );
		if(!$box) {
			return;
		}
		
		$box->delete();
		
		$this->boxes = null;
	}
	
	public function addBox( Product_Box $new_box ) : void
	{
		$new_box->save();
		$this->boxes = null;
	}
	
}