<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoPages;

use Jet\Logger;
use JetApplication\Admin_Managers;
use JetApplication\Content_InfoPage as ContentInfoPage;

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

	/**
	 * @var ?MVC_Controller_Router_AddEditDelete
	 */
	protected ?MVC_Controller_Router_AddEditDelete $router = null;

	/**
	 * @var ?ContentInfoPage
	 */
	protected ?ContentInfoPage $content_info_page = null;

	/**
	 *
	 * @return MVC_Controller_Router_AddEditDelete
	 */
	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->content_info_page = ContentInfoPage::get($id));
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

	/**
	 * @param string $current_label
	 */
	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}

	/**
	 *
	 */
	public function listing_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$listing = new Listing();
		$listing->handle();

		$this->view->setVar( 'filter_form', $listing->getFilterForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

		$this->output( 'list' );
	}

	/**
	 *
	 */
	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Content Info Page' ) );

		$content_info_page = new ContentInfoPage();


		$form = $content_info_page->getAddForm();

		if( $content_info_page->catchAddForm() ) {
			$content_info_page->save();

			Logger::success(
				'content_info_page_created',
				'Content Info Page '.$content_info_page->getPageId().' ('.$content_info_page->getId().') created',
				$content_info_page->getId(),
				$content_info_page->getPageId(),
				$content_info_page
			);

			UI_messages::success(
				Tr::_( 'Content Info Page <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $content_info_page->getPageId() ] )
			);

			Http_Headers::reload( ['id'=>$content_info_page->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'content_info_page', $content_info_page );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function edit_Action() : void
	{
		$content_info_page = $this->content_info_page;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit content info page <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $content_info_page->getPageId() ] ) );

		$form = $content_info_page->getEditForm();

		if( $content_info_page->catchEditForm() ) {

			$content_info_page->save();
			Logger::success(
				'content_info_page_updated',
				'Content Info Page '.$content_info_page->getPageId().' ('.$content_info_page->getId().') updated',
				$content_info_page->getId(),
				$content_info_page->getPageId(),
				$content_info_page
			);

			UI_messages::success(
				Tr::_( 'Content Info Page <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $content_info_page->getPageId() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'content_info_page', $content_info_page );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$content_info_page = $this->content_info_page;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Content Info Page detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $content_info_page->getPageId() ] )
		);

		$form = $content_info_page->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'content_info_page', $content_info_page );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$content_info_page = $this->content_info_page;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete content info page  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $content_info_page->getPageId() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$content_info_page->delete();
			Logger::success(
				'content_info_page_deleted',
				'Content Info Page '.$content_info_page->getPageId().' ('.$content_info_page->getId().') deleted',
				$content_info_page->getId(),
				$content_info_page->getPageId(),
				$content_info_page
			);

			UI_messages::info(
				Tr::_( 'Content Info Page <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $content_info_page->getPageId() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'content_info_page', $content_info_page );

		$this->output( 'delete-confirm' );
	}

}