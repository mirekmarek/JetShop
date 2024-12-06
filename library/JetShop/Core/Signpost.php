<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;

use Jet\Form_Field;
use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Entity_WithEShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShops;
use JetApplication\Signpost_Category;
use JetApplication\Signpost_EShopData;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'signposts',
	database_table_name: 'signposts',
)]
abstract class Core_Signpost extends Entity_WithEShopData implements FulltextSearch_IndexDataProvider, Admin_Entity_WithEShopData_Interface
{
	use Admin_Entity_WithEShopData_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Signpost_EShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $eshop_data = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:'
	)]
	protected int $priority = 0;
	
	protected ?array $category_ids = null;
	
	
	public function getEshopData( ?EShop $eshop = null ): Signpost_EShopData
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
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->setPriority( $priority );
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
	
	public function getEditURL() : string
	{
		return '';
		//TODO: return Admin_Managers::Signpost()->getEditURL( $this->id );
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'main',
			image_title:  Tr::_('Main image'),
		);
		
		$this->defineImage(
			image_class:  'pictogram',
			image_title:  Tr::_('Pictogram - Product detail'),
		);
		
	}
	
	public function getDescriptionMode() : bool
	{
		return true;
	}
}