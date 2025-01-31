<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\DataListing_Column;
use Jet\DataModel_Fetch_Instances;
use Jet\DataListing;
use Jet\Factory_MVC;
use Jet\Http_Request;
use Jet\MVC_View;
use JetApplication\Admin_EntityManager_Module;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\Admin_Managers;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_HasActivationByTimePlan_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasInternalParams_Interface;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShops;


class Listing extends DataListing {
	
	
	protected EShopEntity_Basic|EShopEntity_Admin_Interface $entity;
	protected Admin_EntityManager_Module $entity_manager;
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
		
		$default_schema = [];
		$default_order_by = '-id';
		
		$this->addFilter( $this->init_SearchFilter() );
		$this->addColumn( new Listing_Column_Edit() );
		$this->addColumn( new Listing_Column_ID() );
		
		$default_schema[] = Listing_Column_ID::KEY;
		
		$this->addExport( new Listing_Export_CSV() );
		$this->addExport( new Listing_Export_XLSX() );
		
		
		
		if(
			($this->entity instanceof EShopEntity_HasEShopRelation_Interface ) &&
			EShops::isMultiEShopMode()
		) {
			$this->addColumn( new Listing_Column_EShop() );
			
			$this->addFilter( new Listing_Filter_EShop() );
			
			$default_schema[] = Listing_Column_EShop::KEY;
		}
		
		
		if(
			$this->entity instanceof EShopEntity_HasActivation_Interface
		) {
			$this->addColumn( new Listing_Column_ActiveState() );
			
			$default_schema[] = Listing_Column_ActiveState::KEY;
			
			$this->addFilter( new Listing_Filter_IsActive() );
		}
		
		if( $this->entity instanceof EShopEntity_HasActivationByTimePlan_Interface ) {
			$this->addColumn( new Listing_Column_ValidFrom() );
			$this->addColumn( new Listing_Column_ValidTill() );

			$default_schema[] = Listing_Column_ValidFrom::KEY;
			$default_schema[] = Listing_Column_ValidTill::KEY;
		}
		
		if( $this->entity instanceof EShopEntity_HasInternalParams_Interface ) {
			$this->addColumn( new Listing_Column_InternalName() );
			$this->addColumn( new Listing_Column_InternalCode() );
			$this->addColumn( new Listing_Column_InternalNotes() );
			
			$default_schema[] = Listing_Column_InternalName::KEY;
			$default_schema[] = Listing_Column_InternalCode::KEY;
			$default_schema[] = Listing_Column_InternalNotes::KEY;
			
			$default_order_by = '+internal_name';
		}
		
		$this->setDefaultColumnsSchema( $default_schema );
		
		$this->setDefaultSort($default_order_by);
		
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
	
	
	
	
	
	
	public function getEntity(): EShopEntity_Admin_Interface|EShopEntity_Basic
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
	
	protected bool $handled = false;
	
	public function handle(): void
	{
		if(!$this->handled) {
			$this->handleSchemaManagement();
			parent::handle();
			$this->handeExports();

			$this->handled = true;
		}
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
	
	
	public function getEntityManager(): Admin_EntityManager_Module
	{
		return $this->entity_manager;
	}
	
	
	
	/**
	 * @return EShopEntity_Basic[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getItemList(): DataModel_Fetch_Instances
	{
		return $this->entity::getList();
	}
	
	protected function getIdList(): array
	{
		if( $this->all_ids === null ) {
			$this->all_ids = $this->entity::dataFetchCol(
				select:['id'],
				where: $this->getFilterWhere(),
				order_by: $this->getQueryOrderBy()
			);
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
	
	public function itemGetter( int|string $id ): ?EShopEntity_Basic
	{
		return $this->entity::get( $id );
	}
	
	public function setDefaultColumnsSchema( array $schema ) : void
	{
		Listing_Schema::setDefaultColSchema( $schema );
	}
	
	public function getPrevEditUrl( int $current_id ): string
	{
		$this->handle();
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
		$this->handle();
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
	
	public function getEditUrl( EShopEntity_Basic $item ): string
	{
		return Http_Request::currentURI( ['id' => $item->getId()] );
	}
	
}