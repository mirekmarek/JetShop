<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;

use Jet\Form_Field;
use JetApplication\Admin_Managers;
use JetApplication\Entity_WithShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Shops;
use JetApplication\Signpost_Category;
use JetApplication\Signpost_ShopData;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'signposts',
	database_table_name: 'signposts',
)]
abstract class Core_Signpost extends Entity_WithShopData implements FulltextSearch_IndexDataProvider
{
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Signpost_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:'
	)]
	protected int $priority = 0;
	
	protected ?array $category_ids = null;
	
	
	public function getShopData( ?Shops_Shop $shop = null ): Signpost_ShopData
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
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
		foreach( Shops::getList() as $shop ) {
			$this->getShopData( $shop )->setPriority( $priority );
		}
	}
	
	
	public function getCategoryIds(): array|bool
	{
		if( $this->category_ids === null ) {
			$this->category_ids = Signpost_Category::dataFetchCol(
				select: ['category_id'],
				where: ['signpost_id' => $this->id],
				order_by: ['priority'],
				raw_mode: true
			);
		}
		
		return $this->category_ids;
	}
	
	
	public function addCategory( int $category_id ): bool
	{
		$ids = $this->getCategoryIds();
		if( in_array( $category_id, $ids ) ) {
			return false;
		}
		
		$new = new Signpost_Category();
		$new->setSignpostId( $this->id );
		$new->setCategoryId( $category_id );
		$new->setPriority( count( $this->category_ids ) );
		$new->save();
		
		$this->category_ids[] = $category_id;
		
		return true;
	}
	
	public function removeCategory( int $category_id ): bool
	{
		Signpost_Category::dataDelete( [
			'signpost_id' => $this->id,
			'AND',
			'category_id' => $category_id
		] );
		
		$i = 0;
		foreach( Signpost_Category::fetchInstances() as $c ) {
			$c->setPriority( $i );
			$c->save();
			$i++;
		}
		
		$this->category_ids = null;
		
		return true;
	}
	
	public function sortCategories( array $sort ): void
	{
		$i = 0;
		foreach($sort as $id) {
			$c = Signpost_Category::load([
				'signpost_id' => $this->id,
				'AND',
				'category_id' => $id
			]);
			
			if($c) {
				$c->setPriority($i);
				$c->save();
				$i++;
			}
		}
	}
	
	public function removeAllCategories() : bool
	{
		Signpost_Category::dataDelete([
			'signpost_id' => $this->id
		]);
		
		
		$this->category_ids = null;
		
		return true;
	}
	
	
	
}