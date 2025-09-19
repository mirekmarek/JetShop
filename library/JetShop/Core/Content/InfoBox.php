<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\MVC_Cache;
use JetApplication\Application_Service_Admin_Content_InfoBoxes;
use JetApplication\Content_InfoBox_EShopData;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShop;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShops;


#[DataModel_Definition(
	name: 'content_info_box',
	database_table_name: 'content_info_box',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Info box',
	admin_manager_interface: Application_Service_Admin_Content_InfoBoxes::class,
	separate_tab_form_shop_data: true
)]
abstract class Core_Content_InfoBox extends EShopEntity_WithEShopData implements EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_Admin_WithEShopData_Trait;
	
	/**
	 * @var Content_InfoBox_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_InfoBox_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function getEshopData( ?EShop $eshop = null ): Content_InfoBox_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	public function deactivate() : void
	{
		parent::deactivate();
		foreach(EShops::getList() as $eshop) {
			$ed = $this->getEshopData( $eshop );
			$ed->cacheDelete();
		}
		MVC_Cache::resetOutputCache();
	}
	
	public function activate() : void
	{
		parent::activate();
		foreach(EShops::getList() as $eshop) {
			$ed = $this->getEshopData( $eshop );
			$ed->cacheDelete();
		}
		MVC_Cache::resetOutputCache();
	}
}