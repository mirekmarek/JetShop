<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Admin_Managers;
use JetApplication\Entity_WithShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\PropertyGroup_ShopData;
use JetApplication\Shops_Shop;
use JetApplication\KindOfProduct_PropertyGroup;

#[DataModel_Definition(
	name: 'property_group',
	database_table_name: 'property_groups',
)]
abstract class Core_PropertyGroup extends Entity_WithShopData implements FulltextSearch_IndexDataProvider
{
	
	/**
	 * @var PropertyGroup_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: PropertyGroup_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	public function getShopData( ?Shops_Shop $shop = null ): PropertyGroup_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
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
	
	public function getShopFulltextTexts( Shops_Shop $shop ): array
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