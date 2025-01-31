<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\OrderPersonalReceipt\Overview;


use Jet\Http_Headers;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Order;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable(): string
	{
		return 'Order Personal Receipt';
	}
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Warehouse() );
		$this->listing_manager->addColumn( new Listing_Column_Created() );
		$this->listing_manager->addColumn( new Listing_Column_DispatchDate() );
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Context() );
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Warehouse() );
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search_separated = explode( ' ', $search );
			
			$q = [];
			
			$q['number'] = $search;
			
			$orders = Order::dataFetchCol(select: ['id'], where: ['number'=>$search], raw_mode: true);
			if($orders) {
				$q[] = 'OR';
				$q['order_id'] = $orders;
			}
			
			return $q;
			
		} );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			'created',
			'dispatch_date',
			'number',
			'status',
			'recipient',
			'order_number',
			'context',
			'carrier',
			'tracking_number'
		]);
		
	}
	
	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$this->content->output( $this->getListing()->renderListing() );
	}


	public function add_Action() : void
	{
	}
	
	public function edit_main_Action() : void
	{
		Http_Headers::movedTemporary( $this->current_item->getEditUrl() );
	}
}