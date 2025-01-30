<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_PropertyGroup;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShopEntity_Definition;
use JetApplication\PropertyGroup_EShopData;
use JetApplication\EShop;
use JetApplication\KindOfProduct_PropertyGroup;

#[DataModel_Definition(
	name: 'property_group',
	database_table_name: 'property_groups',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_PropertyGroup::class,
	description_mode: true,
	separate_tab_form_shop_data: true,
	images: [
		'main' => 'Main image',
		'pictogram' => 'Pictogram image',
	]
)]
abstract class Core_PropertyGroup extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	FulltextSearch_IndexDataProvider,
	EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_WithEShopData_HasImages_Trait;
	use EShopEntity_Admin_WithEShopData_Trait;
	
	/**
	 * @var PropertyGroup_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: PropertyGroup_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	public function getEshopData( ?EShop $eshop = null ): PropertyGroup_EShopData
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
		return [
			$this->getInternalName(),
			$this->getInternalCode()
		];
	}
	
	public function getShopFulltextTexts( EShop $eshop ): array
	{
		return [];
	}
	
	public function updateFulltextSearchIndex(): void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex(): void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	public function getUsageKindOfProductIds(): array
	{
		return KindOfProduct_PropertyGroup::dataFetchCol(
			select: [
				'kind_of_product_id'
			],
			where: [
				'group_id' => $this->getId()
			]
		);
	}
}