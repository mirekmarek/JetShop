<?php
namespace JetApplicationModule\Admin\Catalog\Suppliers;

use Jet\DataListing;
use Jet\DataModel_Fetch_Instances;
use Jet\MVC_View;

/**
 *
 */
class Listing extends DataListing {
	
	
	protected MVC_View $column_view;
	protected MVC_View $filter_view;
	
	
	public function __construct( MVC_View $column_view, MVC_View $filter_view )
	{
		$this->column_view = $column_view;
		$this->filter_view = $filter_view;
		
		$this->addColumn( new Listing_Column_Edit() );
		$this->addColumn( new Listing_Column_ID() );
		$this->addColumn( new Listing_Column_InternalName() );
		
		$this->setDefaultSort('+name');
		
		$this->addFilter( new Listing_Filter_Search() );
		
	}
	
	/**
	 * @return Supplier[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getItemList(): DataModel_Fetch_Instances
	{
		return Supplier::getList();
	}
	
	protected function getIdList(): array
	{
		return [];
	}
	
	public function getFilterView(): MVC_View
	{
		return $this->filter_view;
	}
	
	public function getColumnView(): MVC_View
	{
		return $this->column_view;
	}
	
	public function itemGetter( int|string $id ): ?Supplier
	{
		return Supplier::get( $id );
	}
	
}