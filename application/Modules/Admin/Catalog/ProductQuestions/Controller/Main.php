<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Application_Service_Admin;
use JetApplication\Product;
use JetApplication\ProductQuestion;
use JetApplication\ProductQuestion_Status_AnsweredDisplayed;
use JetApplication\ProductQuestion_Status_AnsweredNotDisplayed;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorName() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorEmail() );
		$this->listing_manager->addColumn( new Listing_Column_Created() );
		$this->listing_manager->addColumn( new Listing_Column_Source() );
		$this->listing_manager->addColumn( new Listing_Column_IsSpam() );
		
		$this->listing_manager->setSearchWhereCreator(  function( string $search ) : array {
			
			$q['author_name *'] = '%'.$search.'%';
			
			$q[] = 'OR';
			$q['author_email *'] = '%'.$search.'%';
			
			$products = Application_Service_Admin::FulltextSearch()->search( Product::getEntityType(), $search );
			if($products) {
				$q[] = 'OR';
				$q['product_id'] = $products;
			}
			
			return $q;
			
		} );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'status',
			'product_id',
			'created',
			'author_name',
			'author_email',
			'source'
		]);
	}
	
	protected function edit_main_initPlugins(): void
	{
		Plugin::initPlugins( $this->view, $this->current_item );
		$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
		
		if(Main::getCurrentUserCanEdit()) {
			Plugin::handlePlugins();
		}
	}
	
	public function edit_main_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		/**
		 * @var ProductQuestion $item
		 */
		$item = $this->current_item;
		
		$this->edit_main_handleActivation();
		
		$form = $item->getEditMainForm();
		
		if( $item->catchEditMainForm() ) {
			
			$item->save();
			
			UI_messages::success(
				$this->generateText_edit_main_msg()
			);
			
			$action = Http_Request::POST()->getString('action');
			switch($action) {
				case 'answer_and_display':
					$item->setStatus( ProductQuestion_Status_AnsweredDisplayed::get() );
					break;
				case 'answer_and_do_not_display':
					$item->setStatus( ProductQuestion_Status_AnsweredNotDisplayed::get() );
					break;
			}
			
			Http_Headers::reload();
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $item );
		
		$this->edit_main_initPlugins();
		
		$this->content->output(
			$this->getEditorManager()->renderEditMain( $form )
		);

	}
	
}