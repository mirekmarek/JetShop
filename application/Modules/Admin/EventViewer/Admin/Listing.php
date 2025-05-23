<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\EventViewer\Admin;


use Jet\DataListing;
use Jet\DataModel_Fetch_Instances;
use Jet\MVC_View;


class Listing extends DataListing {
	
	protected MVC_View $column_view;
	protected MVC_View $filter_view;
	
	public function __construct( MVC_View $column_view, MVC_View $filter_view )
	{
		$this->column_view = $column_view;
		$this->filter_view = $filter_view;
		
		$this->addColumn( new Listing_Column_ID() );
		$this->addColumn( new Listing_Column_DateTime() );
		$this->addColumn( new Listing_Column_EventClass() );
		$this->addColumn( new Listing_Column_Event() );
		$this->addColumn( new Listing_Column_EventMessage() );
		$this->addColumn( new Listing_Column_ContextObjectId() );
		$this->addColumn( new Listing_Column_ContextObjectName() );
		$this->addColumn( new Listing_Column_UserId() );
		$this->addColumn( new Listing_Column_UserName() );
		
		$this->setDefaultSort( '-id' );
		
		$this->addFilter( new Listing_Filter_Search() );
		$this->addFilter( new Listing_Filter_EventClass() );
		$this->addFilter( new Listing_Filter_Event() );
		$this->addFilter( new Listing_Filter_DateTime() );
		$this->addFilter( new Listing_Filter_User() );
		$this->addFilter( new Listing_Filter_ContextObject() );
		
		$this->addExport( new Listing_Export_CSV() );
	}
	
	
	protected function getItemList(): DataModel_Fetch_Instances
	{
		return Event::getList();
	}
	
	protected function getIdList(): array
	{
		$ids = Event::fetchIDs( $this->getFilterWhere() );
		$ids->getQuery()->setOrderBy( $this->getQueryOrderBy() );
		
		return $ids->toArray();
	}
	
	public function itemGetter( int|string $id ): ?Event
	{
		return Event::get( $id );
	}
	
	public function getFilterView(): MVC_View
	{
		return $this->filter_view;
	}
	
	public function getColumnView(): MVC_View
	{
		return $this->column_view;
	}
	
	public function getItemURI( int $item_id ) : string
	{
		$this->setParam('id', $item_id );
		
		$URI = $this->getURI();
		
		$this->unsetParam('id');
		
		return $URI;
	}
	
}