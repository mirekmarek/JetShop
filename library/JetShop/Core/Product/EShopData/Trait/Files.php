<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product;
use JetApplication\Product_File;

trait Core_Product_EShopData_Trait_Files
{
	/**
	 * @var Product_File[]
	 */
	protected ?array $files = null;
	
	/**
	 * @return Product_File[]
	 */
	public function getFiles() : array
	{
		if( $this->files===null ) {
			
			$id = $this->getId();
			
			if($this->getType()==Product::PRODUCT_TYPE_VARIANT) {
				$id = $this->getVariantMasterProductId();
			}
			
			$this->files = Product_File::getFiles( $id );
		}
		
		return $this->files;
	}
	
}