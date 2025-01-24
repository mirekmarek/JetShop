<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Admin_Managers_KindOfProductFile;
use JetApplication\JetShopEntity_Definition;
use JetApplication\Product_KindOfFile_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'products_kind_of_file',
	database_table_name: 'products_kind_of_file',
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_KindOfProductFile::class
)]
abstract class Core_Product_KindOfFile extends Entity_WithEShopData implements Admin_Entity_WithEShopData_Interface
{
	use Admin_Entity_WithEShopData_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Show on product detail'
	)]
	protected bool $show_on_product_detail = true;
	
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

	public function getShowOnProductDetail(): bool
	{
		return $this->show_on_product_detail;
	}
	
	public function setShowOnProductDetail( bool $show_on_product_detail ): void
	{
		$this->show_on_product_detail = $show_on_product_detail;
		foreach($this->eshop_data as $sd) {
			$sd->setShowOnProductDetail( $show_on_product_detail );
		}
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
