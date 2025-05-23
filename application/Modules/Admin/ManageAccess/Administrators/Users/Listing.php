<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Users;


use JetApplication\Auth_Administrator_User as User;

use Jet\DataModel_Fetch_Instances;
use Jet\MVC_View;
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
		$this->addColumn( new Listing_Column_UserName() );
		$this->addColumn( new Listing_Column_FirstName() );
		$this->addColumn( new Listing_Column_Surname() );
		$this->addColumn( new Listing_Column_Roles() );
		$this->addColumn( new Listing_Column_IsBlocked() );
		
		
		$this->addFilter( new Listing_Filter_Search() );
		$this->addFilter( new Listing_Filter_Role() );
		$this->addFilter( new Listing_Filter_IsBlocked() );
		
		$this->addOperation( new Listing_Operation_Block() );
		$this->addOperation( new Listing_Operation_Unblock() );
		
	}
	
	
	protected function getItemList(): DataModel_Fetch_Instances
	{
		return User::fetchInstances();
	}
	
	protected function getIdList(): array
	{
		$ids = User::fetchIDs( $this->getFilterWhere() );
		$ids->getQuery()->setOrderBy( $this->getQueryOrderBy() );

		return $ids->toArray();
	}
	
	public function getFilterView(): MVC_View
	{
		return $this->filter_view;
	}
	
	public function getColumnView(): MVC_View
	{
		return $this->column_view;
	}
	
	public function itemGetter( int|string $id ): ?User
	{
		return User::get( $id );
	}
}