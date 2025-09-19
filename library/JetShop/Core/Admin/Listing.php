<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataListing;
use Jet\DataListing_Operation;
use Jet\DataModel_Fetch_Instances;
use Jet\Factory_MVC;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_View;
use JetApplication\Admin_EntityManager_Module;
use JetApplication\Admin_Listing_Column;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Admin_Listing_Handler;
use JetApplication\Admin_Listing_Operation;
use JetApplication\Admin_Listing_Schema;
use JetApplication\Admin_Listing_Schema_Manager;
use JetApplication\Application_Service_Admin_EShopEntity_Listing;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Basic;


abstract class Core_Admin_Listing extends DataListing
{
	
	
	protected EShopEntity_Basic|EShopEntity_Admin_Interface $entity;
	protected Admin_EntityManager_Module $entity_manager;
	protected MVC_View $column_view;
	protected MVC_View $filter_view;
	
	protected Admin_Listing_Schema_Manager $schema_manager;
	
	
	/**
	 * @var Admin_Listing_Handler[]
	 */
	protected array $handlers = [];
	
	
	protected bool $select_items_enabled = false;
	protected bool $handled = false;
	
	protected ?array $nearest_ids = null;
	
	
	public function __construct( Application_Service_Admin_EShopEntity_Listing $listing_manager )
	{
		$this->entity = $listing_manager->getEntity();
		$this->entity_manager = $listing_manager->getEntityManager();
		
		$column_view = Factory_MVC::getViewInstance( $this->entity_manager->getViewsDir().'list/column/' );
		$column_view->setVar('listing', $this);
		$filter_view = Factory_MVC::getViewInstance( $this->entity_manager->getViewsDir().'list/filter/' );
		$column_view->setVar('listing', $this);
		
		$this->column_view = $column_view;
		$this->filter_view = $filter_view;
		
		$this->schema_manager = new Admin_Listing_Schema_Manager( $this );
	}
	
	
	public function getNearestIds() : array
	{
		if($this->nearest_ids === null) {
			$page_no = $this->getPageNo()-1;
			if($page_no<0) {
				$page_no = 0;
			}
			$limit = $this->getItemsPerPage()*3;
			
			$this->nearest_ids = $this->entity::dataFetchCol(
				select:['id'],
				where: $this->getFilterWhere(),
				order_by: $this->getQueryOrderBy(),
				limit: $limit,
				offset: $page_no*$this->getItemsPerPage(),
			);
			
		}
		
		return $this->nearest_ids;
	}
	
	
	public function getEntity(): EShopEntity_Admin_Interface|EShopEntity_Basic
	{
		return $this->entity;
	}
	
	public function getEntityManager(): Admin_EntityManager_Module
	{
		return $this->entity_manager;
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
	
	public function addHandler( Admin_Listing_Handler $handler ) : void
	{
		$this->handlers[$handler->getKey()] = $handler;
		$handler->setListing( $this );
	}
	
	public function handlerExists( string $key ) : bool
	{
		return isset($this->handlers[$key]);
	}
	
	public function handler( string $key ) : Admin_Listing_Handler
	{
		return $this->handlers[$key];
	}
	
	/**
	 * @return Admin_Listing_Handler[]
	 */
	public function getHandlers(): array
	{
		return $this->handlers;
	}
	
	
	public function getPrevEditUrl( int $current_id ): string
	{
		$this->handle();
		$all_ids = $this->getNearestIds();
		
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
		$all_ids = $this->getNearestIds();
		
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
	
	public function getSelectItemsEnabled(): bool
	{
		return $this->select_items_enabled;
	}
	
	public function setSelectItemsEnabled( bool $select_items_enabled ): void
	{
		$this->select_items_enabled = $select_items_enabled;
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
	
	
	
	protected function handleExports() : void
	{
		$types = array_keys( $this->getExportTypes() );
		$export = Http_Request::GET()->getString(
			key: 'export',
			valid_values: $types
		);
		
		
		if( $export ) {
			set_time_limit(-1);
			$this->export( $export )->export();
		}
	}
	
	public function handleHandlers() : void
	{
		foreach( $this->handlers as $handler ) {
			if($handler->canBeHandled()) {
				$handler->handle();
			}
		}
	}
	
	public function handleOperations() : void
	{
		foreach( $this->operations as $operation ) {
			if($operation->canBeHandled()) {
				$operation->perform();
			}
		}
	}
	
	
	public function addOperation( DataListing_Operation|Admin_Listing_Operation $operation ) : void
	{
		$this->setSelectItemsEnabled( true );
		$operation->setListing( $this );
		$this->operations[ $operation->getKey() ] = $operation;
	}
	
	/**
	 * @return Admin_Listing_Operation[]
	 */
	public function getOperations() : array
	{
		return $this->operations;
	}
	
	
	public function handle(): void
	{
		if(!$this->handled) {
			$this->schema_manager->handle();
			parent::handle();
			$this->handleExports();
			$this->handleOperations();
			$this->handleHandlers();
			
			$this->handled = true;
		}
	}
	
	/**
	 * @return Admin_Listing_Column[]
	 */
	public function getVisibleColumns() : array
	{
		$schema = $this->schema_manager->getCurrentColSchema();
		
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
	
	
	public function setDefaultColumnsSchema( array $schema ) : void
	{
		$this->schema_manager->setDefaultColSchema( $schema );
	}
	
	public function getSelectedSchemaDefinition() : ?Admin_Listing_Schema
	{
		return $this->schema_manager->getSelectedSchemaDefinition();
	}
	
	public function getSchemaManager(): Admin_Listing_Schema_Manager
	{
		return $this->schema_manager;
	}
	
	public function filterIsActive() : bool
	{
		foreach($this->filters as $filter) {
			if( $filter->isActive() ) {
				return true;
			}
		}
		
		return false;
	}
	
	protected function catchFilterForm(): void
	{
		$form = $this->getFilterForm();
		
		if(
			$form->catchInput() &&
			$form->validate()
		) {
			foreach( $this->filters as $filter ) {
				$filter->catchForm( $form );
			}
			
			$this->setPageNo( 1 );
			
			foreach($this->filters as $filter) {
				/**
				 * @var Admin_Listing_Filter $filter
				 */
				if($filter->tryDirectOpen()) {
					$ids = $this->getNearestIds();
					if(count($ids)==1) {
						Http_Headers::movedTemporary( $this->getURI().'&id='.$ids[0] );
					}
				}
			}
			
			Http_Headers::movedTemporary( $this->getURI() );
		}
	}
	
}