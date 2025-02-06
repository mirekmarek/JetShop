<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Brand;
use JetApplication\Brand_EShopData;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShop_Managers;
use JetApplication\EShop;
use JetApplication\EShopEntity_Definition;

#[DataModel_Definition(
	name: 'brands',
	database_table_name: 'brands',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Brand',
	admin_manager_interface: Admin_Managers_Brand::class,
	description_mode: true,
	separate_tab_form_shop_data: true,
	images: [
		'logo' => 'Logo',
		'big_logo' => 'Big logo',
		'title' => 'Title image',
	]
)]
abstract class Core_Brand extends EShopEntity_WithEShopData implements
	FulltextSearch_IndexDataProvider,
	EShopEntity_HasImages_Interface,
	EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_WithEShopData_HasImages_Trait;
	use EShopEntity_Admin_WithEShopData_Trait;
	
	/**
	 * @var Brand_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Brand_EShopData::class
	)]
	protected array $eshop_data = [];
	
	public function getEshopData( ?EShop $eshop=null ) : Brand_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	
	public function getFulltextObjectType(): string
	{
		return '';
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getShopFulltextTexts( EShop $eshop ): array
	{
		$sd = $this->getEshopData( $eshop );
		
		return [$sd->getName()];
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
		EShop_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
		EShop_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
}