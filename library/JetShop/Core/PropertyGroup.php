<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_PropertyGroup;
use JetApplication\Entity_WithEShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\JetShopEntity_Definition;
use JetApplication\PropertyGroup_EShopData;
use JetApplication\EShop;
use JetApplication\KindOfProduct_PropertyGroup;

#[DataModel_Definition(
	name: 'property_group',
	database_table_name: 'property_groups',
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_PropertyGroup::class
)]
abstract class Core_PropertyGroup extends Entity_WithEShopData implements FulltextSearch_IndexDataProvider, Admin_Entity_WithEShopData_Interface
{
	use Admin_Entity_WithEShopData_Trait;
	
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