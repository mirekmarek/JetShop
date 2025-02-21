<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Roles;


use Jet\MVC_View;
use Jet\DataModel_Fetch_Instances;
use JetApplication\Auth_Administrator_Role as Role;

use Jet\DataListing;



class Listing extends DataListing
{
	protected MVC_View $column_view;
	protected MVC_View $filter_view;
	
	
	public function __construct( MVC_View $column_view, MVC_View $filter_view )
	{
		$this->column_view = $column_view;
		$this->filter_view = $filter_view;
		
		$this->addColumn( new Listing_Column_Edit() );
		$this->addColumn( new Listing_Column_ID() );
		$this->addColumn( new Listing_Column_Name() );
		$this->addColumn( new Listing_Column_Description() );
		
		$this->setDefaultSort('+name');
		
		$this->addFilter( new Listing_Filter_Search() );
		
	}
	
	
	/**
	 * @return Role[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getItemList(): DataModel_Fetch_Instances
	{
		return Role::getList();
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
	
	public function itemGetter( int|string $id ): ?Role
	{
		return Role::get( $id );
	}
	
}