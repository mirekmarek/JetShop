<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Product_KindOfFile_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'products_kind_of_file',
	database_table_name: 'products_kind_of_file',
)]
abstract class Core_Product_KindOfFile extends Entity_WithEShopData implements Admin_Entity_WithEShopData_Interface
{
	use Admin_Entity_WithEShopData_Trait;
	
	/**
	 * @var Product_KindOfFile_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_KindOfFile_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	public function getEshopData( ?EShop $eshop=null ) : Product_KindOfFile_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	public function getEditURL() : string
	{
		//TODO:
		return '';
		//return Admin_Managers::KindOfFile()->getEditURL( $this->id );
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
	
	public function getDescriptionMode() : bool
	{
		return true;
	}
}
