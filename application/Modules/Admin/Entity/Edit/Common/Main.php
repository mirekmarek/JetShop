<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Entity\Edit\Common;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Translator;
use Jet\UI_tabs;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Managers_Entity_Edit_Common;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Entity_Edit_Common
{
	public function renderToolbar(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?Form                                                   $form = null,
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $item);
		$view->setVar('form', $form);
		$view->setVar('listing', $listing);

		$view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $view->render( 'edit/toolbar' );
		
	}

	public function renderEditMain(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?UI_tabs                                                $tabs=null,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?callable                                               $common_data_fields_renderer=null,
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $item);
		$view->setVar('listing', $listing);
		$view->setVar('tabs', $tabs);
		$view->setVar('form', $item->getEditForm());
		$view->setVar('common_data_fields_renderer', $common_data_fields_renderer);
		$view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $view->render( 'edit/main' );
		
	}
	
	public function renderEditImages(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?UI_tabs                                                $tabs=null,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $item);
		$view->setVar('listing', $listing);
		$view->setVar('tabs', $tabs);
		$view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $view->render( 'edit/images' );
		
	}
	
	public function renderAdd(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?UI_tabs $tabs=null,
		?callable $common_data_fields_renderer=null,
		?callable $shop_data_fields_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $item);
		$view->setVar('tabs', $tabs);
		$view->setVar('form', $item->getAddForm());
		$view->setVar('common_data_fields_renderer', $common_data_fields_renderer);
		$view->setVar('shop_data_fields_renderer', $shop_data_fields_renderer);
		
		return $view->render( 'add' );
		
	}
	
	public function renderDeleteConfirm(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		string $message
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $item);
		$view->setVar('message', $message);
		
		return $view->render( 'delete/confirm' );
		
	}
	
	public function renderDeleteNotPossible(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		string $message,
		?callable $reason_renderer=null
	): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $item);
		$view->setVar('message', $message);
		$view->setVar('reason_renderer', $reason_renderer);
		
		return $view->render( 'delete/not-possible' );
	}
	
	public function renderShowName( int $id, Entity_Basic|Admin_Entity_Common_Interface $entity ): string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($id, $entity) {
				$item = $entity::get($id);
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('id', $id);
				
				if($item) {
					$view->setVar('item', $item);
					return $view->render('show-name/known');
				}
				
				return $view->render('show-name/unknown');
			}
		);
	}
	
}