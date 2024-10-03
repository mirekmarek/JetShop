<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Admin_Managers;
use JetApplication\Brand_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Shop_Managers;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'brands',
	database_table_name: 'brands',
)]
abstract class Core_Brand extends Entity_WithShopData implements FulltextSearch_IndexDataProvider {
	
	/**
	 * @var Brand_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Brand_ShopData::class
	)]
	protected array $shop_data = [];
	
	public function getShopData( ?Shops_Shop $shop=null ) : Brand_ShopData
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
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getShopFulltextTexts( Shops_Shop $shop ): array
	{
		$sd = $this->getShopData( $shop );
		
		return [$sd->getName()];
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
		Shop_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
		Shop_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
}