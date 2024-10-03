<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProductFile;

use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Product_KindOfFile;

class KindOfProductFile extends Product_KindOfFile implements Admin_Entity_WithShopData_Interface {
	use Admin_Entity_WithShopData_Trait;
	
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	
	public function defineImages() : void
	{
		
		$this->defineImage(
			image_class:  'main',
			image_title:  Tr::_('Main image')
		);
		$this->defineImage(
			image_class:  'pictogram',
			image_title:  Tr::_('Pictogram image')
		);
	}

}