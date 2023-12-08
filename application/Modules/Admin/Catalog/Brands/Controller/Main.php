<?php
namespace JetApplicationModule\Admin\Catalog\Brands;


use Jet\Logger;
use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;

use Jet\MVC_Controller_Default;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Factory_MVC;

use JetApplication\Admin_Managers;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	
	protected ?Brand $brand = null;

	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	
	protected ?Listing $listing = null;
	
	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					$this->brand = Brand::get((int)$id);
					$this->brand?->setEditable(Main::getCurrentUserCanEdit());
					return (bool)($this->brand);
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => Main::ACTION_GET,
					'add'    => Main::ACTION_ADD,
					'edit'   => Main::ACTION_GET,
					'delete' => Main::ACTION_DELETE,
				]
			);
		}

		return $this->router;
	}

	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}
	
	protected function getListing() : Listing
	{
		if(!$this->listing) {
			$column_view = Factory_MVC::getViewInstance( $this->view->getScriptsDir().'list/column/' );
			$column_view->setController( $this );
			$filter_view = Factory_MVC::getViewInstance( $this->view->getScriptsDir().'list/filter/' );
			$filter_view->setController( $this );
			
			$this->listing = new Listing(
				column_view: $column_view,
				filter_view: $filter_view
			);
		}
		
		return $this->listing;
	}
	
	/**
	 *
	 */
	public function listing_Action() : void
	{
		$listing = $this->getListing();
		$listing->handle();
		
		$this->view->setVar( 'listing', $listing );
		
		$this->output( 'list' );
	}

	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Brand' ) );

		$brand = new Brand();


		$form = $brand->getAddForm();

		if( $brand->catchAddForm() ) {
			$brand->save();

			Logger::success(
				event: 'brand_created',
				event_message: 'Brand \''.$brand->getInternalName().'\' ('.$brand->getId().') created',
				context_object_id: $brand->getId(),
				context_object_name: $brand->getInternalName(),
				context_object_data: $brand
			);

			UI_messages::success(
				Tr::_( 'Brand <b>%NAME%</b> has been created', [ 'NAME' => $brand->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$brand->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'brand', $brand );

		$this->output( 'add' );

	}


	public function edit_Action() : void
	{
		$brand = $this->brand;
		
		$brand->handleActivation();
		$brand->handleImages();
		
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit brand <b>%NAME%</b>', [ 'NAME' => $brand->getInternalName() ] ) );
		
		$form = $brand->getEditForm();

		if( $brand->catchEditForm() ) {

			$brand->save();

			Logger::success(
				event: 'brand_updated',
				event_message: 'Brand \''.$brand->getInternalName().'\' ('.$brand->getId().') updated',
				context_object_id: $brand->getId(),
				context_object_name: $brand->getInternalName(),
				context_object_data: $brand
			);

			UI_messages::success(
				Tr::_( 'Brand <b>%NAME%</b> has been updated', [ 'NAME' => $brand->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'brand', $brand );

		$this->output( 'edit' );

	}

	public function view_Action() : void
	{
		$brand = $this->brand;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Brand detail <b>%NAME%</b>', [ 'NAME' => $brand->getInternalName() ] )
		);

		$form = $brand->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'brand', $brand );

		$this->output( 'edit' );

	}

	public function delete_Action() : void
	{
		$brand = $this->brand;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete brand <b>%NAME%</b>', [ 'NAME' => $brand->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$brand->delete();

			Logger::success(
				event: 'brand_deleted',
				event_message: 'Brand \''.$brand->getInternalName().'\' ('.$brand->getId().') deleted',
				context_object_id: $brand->getId(),
				context_object_name: $brand->getInternalName(),
				context_object_data: $brand
			);

			UI_messages::info(
				Tr::_( 'Brand <b>%NAME%</b> has been deleted', [ 'NAME' => $brand->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'brand', $brand );

		$this->output( 'delete-confirm' );
	}

}