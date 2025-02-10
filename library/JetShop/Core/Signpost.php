<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;

use Jet\Form_Field;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Signpost;
use JetApplication\Category;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_CanNotBeDeletedReason;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShops;
use JetApplication\EShopEntity_Definition;
use JetApplication\Signpost_Category;
use JetApplication\Signpost_EShopData;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'signposts',
	database_table_name: 'signposts',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Signpost',
	admin_manager_interface: Admin_Managers_Signpost::class,
	description_mode: true,
	separate_tab_form_shop_data: true,
	images: [
		'main' => 'Main image',
		'pictogram' => 'Pictogram image',
	]
)]
abstract class Core_Signpost extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	FulltextSearch_IndexDataProvider,
	EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_WithEShopData_HasImages_Trait;
	use EShopEntity_Admin_WithEShopData_Trait;
	
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
	
	/**
	 * @param EShopEntity_Basic $entity_to_be_deleted
	 * @param EShopEntity_CanNotBeDeletedReason[] &$reasons
	 * @return bool
	 */
	public static function checkIfItCanBeDeleted( EShopEntity_Basic $entity_to_be_deleted, array &$reasons=[] ) : bool
	{
		/** @noinspection PhpSwitchStatementWitSingleBranchInspection */
		switch( get_class($entity_to_be_deleted) ) {
			case Category::class:
				$ids = Signpost_Category::dataFetchCol(
					select: [ 'signpost_id' ],
					where: ['category_id' => $entity_to_be_deleted->getId() ]
				);
				if($ids) {
					$reasons[] = static::createCanNotBeDeletedReason(
						reason: 'Signpost - category is used',
						ids:    $ids
					);
					
					return false;
				}
				break;
		}
		
		return true;
	}
	
}