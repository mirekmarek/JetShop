<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;

use Jet\AJAX;
use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Entity_Listing;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
use Jet\Navigation_Breadcrumb;
use JetApplication\Product;
use JetApplication\WarehouseManagement_StockCard;


class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	protected ?WarehouseManagement_StockCard $stock_card = null;
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;


	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->stock_card = WarehouseManagement_StockCard::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => '',
					'edit'   => '',
				]
			);
		}

		return $this->router;
	}
	
	protected function setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}
	
	public function getListing() : Admin_Managers_Entity_Listing
	{
		if(!$this->listing_manager) {
			$this->listing_manager = Admin_Managers::EntityListing();
			$this->listing_manager->setUp(
				$this->module
			);
			
			$this->setupListing();
		}
		
		return $this->listing_manager;
	}
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Warehouse() );
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_Cancelled() );
		$this->listing_manager->addColumn( new Listing_Column_Sector() );
		$this->listing_manager->addColumn( new Listing_Column_Rack() );
		$this->listing_manager->addColumn( new Listing_Column_Position() );
		$this->listing_manager->addColumn( new Listing_Column_InStock() );
		$this->listing_manager->addColumn( new Listing_Column_Blocked() );
		$this->listing_manager->addColumn( new Listing_Column_Available() );
		$this->listing_manager->addColumn( new Listing_Column_TotalIn() );
		$this->listing_manager->addColumn( new Listing_Column_TotalOut() );
		
		$this->listing_manager->addFilter( new Listing_Filter_Warehouse() );
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_Product() );
		
		
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$q = [];
			$res = Admin_Managers::FulltextSearch()->search(
				Product::getEntityType(),
				$search
			);
			
			if(!$res) {
				$res = [-1];
			}
			
			$q = ['product_id'=>$res];
			
			return $q;
		} );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			Listing_Column_Warehouse::KEY,
			Listing_Column_Product::KEY,
			Listing_Column_Cancelled::KEY,
			Listing_Column_Sector::KEY,
			Listing_Column_Rack::KEY,
			Listing_Column_Position::KEY,
			Listing_Column_InStock::KEY,
			Listing_Column_Blocked::KEY,
			Listing_Column_Available::KEY,
			Listing_Column_TotalIn::KEY,
			Listing_Column_TotalOut::KEY,
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
	
	public function edit_Action() : void
	{
		Navigation_Breadcrumb::addURL( $this->stock_card->getAdminTitle() );
		
		$this->view->setVar('card', $this->stock_card);
		
		
		
		
		$sector = new Form_Field_Input('sector', '');
		$sector->setDefaultValue( $this->stock_card->getSector() );
		$sector->setFieldValueCatcher( function( string $v ) {
			$this->stock_card->setSector( $v );
		} );
		
		$rack = new Form_Field_Input('rack', '');
		$rack->setDefaultValue( $this->stock_card->getRack() );
		$rack->setFieldValueCatcher( function( string $v ) {
			$this->stock_card->setRack( $v );
		} );
		
		$position = new Form_Field_Input('position', '');
		$position->setDefaultValue( $this->stock_card->getPosition() );
		$position->setFieldValueCatcher( function( string $v ) {
			$this->stock_card->setPosition( $v );
		} );
		
		$change_location_form = new Form('change_location', [
			$sector, $rack, $position
		]);
		
		if(Main::getCurrentUserCanChangeLocation()) {
			if($change_location_form->catch()) {
				UI_messages::success(Tr::_('Location has been changed'));
				Logger::info(
					event: 'stock_card_updated',
					event_message: 'Stock card '.$this->stock_card->getAdminTitle().' updated',
					context_object_id: $this->stock_card->getId(),
					context_object_name: $this->stock_card->getAdminTitle(),
					context_object_data: $this->stock_card
				);
				$this->stock_card->save();
				Http_Headers::reload();
			}
		} else {
			$change_location_form->setIsReadonly();
		}
		
		$GET = Http_Request::GET();
		$action = $GET->getString('do');
		if($action) {
			
			if(
				$action=='reactivate' &&
				Main::getCurrentUserCanCancelOrReactivate()
			) {
				$this->stock_card->reactivate();
			}
			
			if(
				$action=='cancel' &&
				$this->stock_card->getInStock()<=0 &&
				Main::getCurrentUserCanCancelOrReactivate()
			) {
				$this->stock_card->cancel();
			}
			
			if(
				$action=='recalculate' &&
				Main::getCurrentUserCanRecalculate()
			) {
				UI_messages::success(Tr::_('Stock card data has been calculated'));
				
				$this->stock_card->recalculate();
			}
			
			if(
				$action=='show_status_on_date'
			) {
				$date = new Data_DateTime( $GET->getString('date') );
				
				$this->stock_card->recalculateTillDateTime( $date );
				
				AJAX::snippetResponse(
					$this->view->render('status')
				);
			}
			
			Http_Headers::reload(unset_GET_params: ['do']);
		}
		
		$this->getListing();
		
		$this->view->setVar('change_location_form', $change_location_form);
		$this->view->setVar('listing', $this->listing_manager);
		
		$this->output('detail');
	}
	
	
}