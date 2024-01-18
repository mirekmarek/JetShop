<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Logger;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Entity_Listing;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\MVC_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	
	protected ?DiscountsCode $discounts_code = null;
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;
	
	
	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					$this->discounts_code = DiscountsCode::get($id);
					if(!$this->discounts_code) {
						return false;
					}
					
					$this->discounts_code->setEditable( Main::getCurrentUserCanEdit() );
					
					return true;
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => Main::ACTION_GET,
					'add'    => Main::ACTION_ADD,
					'edit'   => Main::ACTION_UPDATE,
					'delete' => Main::ACTION_DELETE,
				]
			);
		}

		return $this->router;
	}
	
	protected function setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();

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
		$this->listing_manager->addColumn( new Listing_Column_Code() );
		$this->listing_manager->addColumn( new Listing_Column_ValidFrom() );
		$this->listing_manager->addColumn( new Listing_Column_ValidTill() );
		$this->listing_manager->addColumn( new Listing_Column_InternalNotes() );
		$this->listing_manager->addColumn( new Listing_Column_DiscountType() );
		$this->listing_manager->addColumn( new Listing_Column_MinimalOrderAmount() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search = '%'.$search.'%';
			
			return [
				'code *' => $search,
				'OR',
				'internal_notes *' => $search,
			];
		} );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'edit',
			'id',
			'shop',
			'code',
			'status',
			'internal_notes',
		]);
	}
	
	
	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$this->content->output( $this->getListing()->renderListing() );
	}
	
	public function add_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_( 'Create a new Discounts Code' ) );

		$discounts_code = new DiscountsCode();


		$form = $discounts_code->getAddForm();

		if( $discounts_code->catchAddForm() ) {
			$discounts_code->save();

			Logger::success(
				'discount_code_created',
				'Discount code '.$discounts_code->getCode().' ('.$discounts_code->getCode().') created',
				$discounts_code->getId(),
				$discounts_code->getCode(),
				$discounts_code
			);

			UI_messages::success(
				Tr::_( 'Discounts Code <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
			);

			Http_Headers::reload( ['id'=>$discounts_code->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function edit_Action() : void
	{
		$discounts_code = $this->discounts_code;

		$this->setBreadcrumbNavigation( Tr::_( 'Edit discounts code <b>%ITEM_NAME%</b>', ['ITEM_NAME' => $discounts_code->getCode() ] ) );

		$form = $discounts_code->getEditForm();

		if( $discounts_code->catchEditForm() ) {

			$discounts_code->save();

			Logger::success(
				'discount_code_updated',
				'Discount code '.$discounts_code->getCode().' ('.$discounts_code->getCode().') updated',
				$discounts_code->getId(),
				$discounts_code->getCode(),
				$discounts_code
			);

			UI_messages::success(
				Tr::_( 'Discounts Code <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$discounts_code = $this->discounts_code;

		$this->setBreadcrumbNavigation(
			Tr::_( 'Discounts Code detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
		);

		$form = $discounts_code->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$discounts_code = $this->discounts_code;

		$this->setBreadcrumbNavigation(
			Tr::_( 'Delete discounts code  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$discounts_code->delete();
			Logger::success(
				'discount_code_deleted',
				'Discount code '.$discounts_code->getCode().' ('.$discounts_code->getCode().') deleted',
				$discounts_code->getId(),
				$discounts_code->getCode(),
				$discounts_code
			);

			UI_messages::info(
				Tr::_( 'Discounts Code <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'delete-confirm' );
	}

}