<?php
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\DataListing_Column;
use Jet\DataModel_Fetch_Instances;
use Jet\DataListing;
use Jet\Factory_MVC;
use Jet\Http_Request;
use Jet\MVC_View;
use JetApplication\Admin_Entity_Common_Manager_Interface;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Entity_Basic;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops;

/**
 *
 */
class Listing extends DataListing {
	
	
	protected Entity_Basic|Admin_Entity_Common_Interface $entity;
	protected Admin_Entity_Common_Manager_Interface $entity_manager;
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
		
		$is_shop_related_entity = ($this->entity instanceof Entity_WithShopRelation);
		
		$this->addColumn( new Listing_Column_Edit() );
		
		if(!$is_shop_related_entity) {
			$this->addColumn( new Listing_Column_ID() );
			$this->addColumn( new Listing_Column_ActiveState() );
			$this->addColumn( new Listing_Column_InternalName() );
			$this->addColumn( new Listing_Column_InternalCode() );
			$this->addColumn( new Listing_Column_InternalNotes() );
			
			$this->addFilter( new Listing_Filter_Search() );
			$this->addFilter( new Listing_Filter_IsActive() );
			
			$this->addExport( new Listing_Export_CSV() );
			$this->addExport( new Listing_Export_XLSX() );
			
			
			$this->setDefaultSort('+internal_name');
		} else {
			
			$this->addColumn( new Listing_Column_ID() );
			
			
			if( Shops::isMultiShopMode() ) {
				$this->addColumn( new Listing_Column_Shop() );
				
				$this->addFilter( new Listing_Filter_Shop() );
			}
			
			$this->addFilter( new Listing_Filter_Search() );
			
			
			$this->setDefaultSort('-id');
		}
		

	}
	
	public function getEntity(): Admin_Entity_Common_Interface|Entity_Basic
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
	
	
	public function getEntityManager(): Admin_Entity_Common_Manager_Interface
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
		$all_ids = $this->getAllIds();
		
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
		$all_ids = $this->getAllIds();
		
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