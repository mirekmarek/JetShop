<?php
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\DataListing_Column;
use Jet\DataModel_Fetch_Instances;
use Jet\DataListing;
use Jet\Factory_MVC;
use Jet\Http_Request;
use Jet\MVC_View;
use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Entity_Basic;
use JetApplication\Entity_Common;
use JetApplication\Entity_Marketing;
use JetApplication\Entity_WithShopData;
use JetApplication\Entity_WithShopRelation;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Shops;

/**
 *
 */
class Listing extends DataListing {
	
	
	protected Entity_Basic|Admin_Entity_Interface $entity;
	protected Admin_EntityManager_Interface $entity_manager;
	protected MVC_View $column_view;
	protected MVC_View $filter_view;
	
	
	public function __construct( Main $main )
	{
		
		$this->entity = $main->getEntity();
		$this->entity_manager = $main->getEntityManager();
		
		$column_view = Factory_MVC::getViewInstance( $this->entity_manager->getViewsDir().'list/column/' );
		$column_view->setVar('listing', $this);
		$filter_view = Factory_MVC::getViewInstance( $this->entity_manager->getViewsDir().'list/filter/' );
		$column_view->setVar('listing', $this);
		
		$this->column_view = $column_view;
		$this->filter_view = $filter_view;
		
		
		if( ($this->entity instanceof Entity_Basic) ) {
			$this->init_Basic();
		}
		
		if( ($this->entity instanceof Entity_WithShopRelation) ) {
			$this->init_WithShopRelation();
		}
		
		if( ($this->entity instanceof Entity_Marketing) ) {
			$this->init_Marketing();
		}
		
		if( ($this->entity instanceof Entity_Common) ) {
			$this->init_Common();
		}
		
		if( ($this->entity instanceof Entity_WithShopData) ) {
			$this->init_WithShopData();
		}
		
	}
	
	protected function init_SearchFilter() : Listing_Filter_Search
	{
		$search = new Listing_Filter_Search();
		if($this->entity instanceof FulltextSearch_IndexDataProvider) {
			
			$entity = $this->entity;
			
			$search->setWhereCreator( function( string $search ) use ($entity) : array {
				
				$ids = Admin_Managers::FulltextSearch()->search(
					$entity::getEntityType(),
					$search
				);
				
				if(!$ids) {
					$ids = [0];
				}
				
				return [
					'id' => $ids,
				];
				
			} );
			
		}
		
		return $search;
	}
	
	protected function init_Basic() : void
	{
		$this->addColumn( new Listing_Column_Edit() );
		
		$this->addFilter( $this->init_SearchFilter() );
		
		$this->setDefaultColumnsSchema([
		] );
		
		
		$this->setDefaultSort('-id');
	}
	
	
	protected function init_WithShopRelation() : void
	{
		$this->addColumn( new Listing_Column_Edit() );
		$this->addColumn( new Listing_Column_ID() );
		
		
		if( Shops::isMultiShopMode() ) {
			$this->addColumn( new Listing_Column_Shop() );
			$this->addFilter( new Listing_Filter_Shop() );
		}
		
		$this->addFilter( $this->init_SearchFilter() );
		
		$this->setDefaultColumnsSchema([
			Listing_Column_ID::KEY,
			Listing_Column_Shop::KEY,
		] );
		
		
		$this->setDefaultSort('-id');
	}
	
	protected function init_Marketing() : void
	{
		$this->addColumn( new Listing_Column_Edit() );
		$this->addColumn( new Listing_Column_ID() );
		
		if( Shops::isMultiShopMode() ) {
			$this->addColumn( new Listing_Column_Shop() );
			$this->addFilter( new Listing_Filter_Shop() );
		}
		
		$this->addColumn( new Listing_Column_ActiveState() );
		
		$this->addColumn( new Listing_Column_InternalName() );
		$this->addColumn( new Listing_Column_InternalCode() );
		
		$this->addColumn( new Listing_Column_ValidFrom() );
		$this->addColumn( new Listing_Column_ValidTill() );
		
		$this->addColumn( new Listing_Column_InternalNotes() );
		
		
		$this->setDefaultColumnsSchema([
			Listing_Column_ID::KEY,
			Listing_Column_Shop::KEY,
			Listing_Column_ActiveState::KEY,
			Listing_Column_InternalName::KEY,
			Listing_Column_InternalCode::KEY,
			
			Listing_Column_ValidFrom::KEY,
			Listing_Column_ValidTill::KEY,
			
			Listing_Column_InternalNotes::KEY
		] );
		
		
		$this->addFilter( $this->init_SearchFilter() );
		$this->addFilter( new Listing_Filter_IsActive() );
		
		$this->addExport( new Listing_Export_CSV() );
		$this->addExport( new Listing_Export_XLSX() );
		
		
		$this->setDefaultSort('+internal_name');
	}
	
	protected function init_WithShopData() : void
	{
		$this->init_Common();
	}
	
	protected function init_Common() : void
	{
		$this->addColumn( new Listing_Column_Edit() );
		$this->addColumn( new Listing_Column_ID() );
		
		$this->addColumn( new Listing_Column_ActiveState() );
		
		$this->addColumn( new Listing_Column_InternalName() );
		$this->addColumn( new Listing_Column_InternalCode() );
		$this->addColumn( new Listing_Column_InternalNotes() );
		
		
		$this->setDefaultColumnsSchema([
			Listing_Column_ID::KEY,
			Listing_Column_ActiveState::KEY,
			Listing_Column_InternalName::KEY,
			Listing_Column_InternalCode::KEY,
			Listing_Column_InternalNotes::KEY
		] );
		
		
		$this->addFilter( $this->init_SearchFilter() );
		
		$this->addFilter( new Listing_Filter_IsActive() );
		$this->addExport( new Listing_Export_CSV() );
		$this->addExport( new Listing_Export_XLSX() );
		
		
		$this->setDefaultSort('+internal_name');
	}
	
	
	public function getEntity(): Admin_Entity_Interface|Entity_Basic
	{
		return $this->entity;
	}
	
	protected function handleSchemaManagement() : void
	{
		Listing_Schema::setListing( $this );
		
		Listing_Schema::handle();
	}
	
	protected function handeExports() : void
	{
		$types = array_keys( $this->getExportTypes() );
		$export = Http_Request::GET()->getString(
			key: 'export',
			valid_values: $types
		);
		
		
		if( $export ) {
			$this->export( $export )->export();
		}
	}
	
	public function handle(): void
	{
		$this->handleSchemaManagement();
		parent::handle();
		$this->handeExports();
	}
	
	/**
	 * @return DataListing_Column[]
	 */
	public function getVisibleColumns() : array
	{
		$schema = Listing_Schema::getCurrentColSchema();
		
		foreach($this->columns as $col) {
			if($col->isMandatory()) {
				continue;
			}
			
			if(
				($index=array_search( $col->getKey(), $schema ))!==false
			) {
				$index++;
				
				$col->setIndex( $index );
				$col->setIsVisible( true );

			} else {
				$col->setIsVisible(false);
			}
		}
		
		return parent::getVisibleColumns();
	}
	
	
	public function getEntityManager(): Admin_EntityManager_Interface
	{
		return $this->entity_manager;
	}
	
	
	
	/**
	 * @return Entity_Basic[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getItemList(): DataModel_Fetch_Instances
	{
		return $this->entity::getList();
	}
	
	protected function getIdList(): array
	{
		if( $this->all_ids === null ) {
			$this->handle();

			$this->all_ids = $this->entity::dataFetchCol(select:['id'], where: $this->getFilterWhere(), order_by: $this->getQueryOrderBy() );
		}
		
		return $this->all_ids;
	}
	
	public function getFilterView(): MVC_View
	{
		return $this->filter_view;
	}
	
	public function getColumnView(): MVC_View
	{
		return $this->column_view;
	}
	
	public function itemGetter( int|string $id ): ?Entity_Basic
	{
		return $this->entity::get( $id );
	}
	
	public function setDefaultColumnsSchema( array $schema ) : void
	{
		Listing_Schema::setDefaultColSchema( $schema );
	}
	
	public function getPrevEditUrl( int $current_id ): string
	{
		$all_ids = $this->getIdList();
		
		$index = array_search( $current_id, $all_ids );
		
		if( $index ) {
			$index--;
			if( isset( $all_ids[$index] ) ) {
				return Http_Request::currentURI( ['id' => $all_ids[$index]] );
			}
		}
		
		return '';
	}
	
	public function getNextEditUrl( int $current_id ): string
	{
		$all_ids = $this->getIdList();
		
		$index = array_search( $current_id, $all_ids );
		if( $index !== false ) {
			$index++;
			if( isset( $all_ids[$index] ) ) {
				return Http_Request::currentURI( ['id' => $all_ids[$index]] );
			}
		}
		
		return '';
	}
	
	public function getEditUrl( Entity_Basic $item ): string
	{
		return Http_Request::currentURI( ['id' => $item->getId()] );
	}
	
}