<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\AccessoriesGroups;

use Jet\AJAX;
use Jet\Http_Request;
use JetApplication\Accessories_Group;
use JetApplication\Admin_EntityManager_Controller;

/**
 *
 */
class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Accessories groups';
	}
	
	
	public function edit_main_Action() : void
	{
		/**
		 * @var Accessories_Group $item
		 */
		$item = $this->current_item;
		
		if(!$item->getEditForm()->getIsReadonly()) {
			$GET = Http_Request::GET();
			if(($add=$GET->getInt('add_product'))) {
				$this->view->setVar( 'item', $item );
				$item->addProduct( $add );
				AJAX::snippetResponse( $this->view->render('edit/main/products') );
			}
			if(($remove=$GET->getInt('remove_product'))) {
				$this->view->setVar( 'item', $item );
				$item->removeProduct( $remove );
				AJAX::snippetResponse( $this->view->render('edit/main/products') );
			}
			if(($sort=$GET->getString('sort'))) {
				$item->sortProducts( explode(',', $sort) );
				AJAX::snippetResponse( '' );
			}
		}
		
		parent::edit_main_Action();
	}
}