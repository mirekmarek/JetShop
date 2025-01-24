<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Entity\Edit\WithEShopRelation;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Translator;
use Jet\UI_tabs;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_Managers_Entity_Edit_WithEShopRelation;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_Basic;
use JetApplication\Entity_WithEShopRelation;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Entity_Edit_WithEShopRelation
{
	protected Entity_Basic|Admin_Entity_Interface $item;
	protected ?Admin_Managers_Entity_Listing $listing = null;
	protected ?UI_tabs $tabs = null;
	
	public function init(
		Entity_Basic|Admin_Entity_Interface $item,
		?Admin_Managers_Entity_Listing      $listing = null,
		?UI_tabs                            $tabs = null
	): void
	{
		$this->item = $item;
		$this->listing = $listing;
		$this->tabs = $tabs;
	}
	
	
	public function renderToolbar(
		?Form                                                   $form = null,
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $this->item);
		$view->setVar('form', $form);
		$view->setVar('listing', $this->listing);

		$view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $view->render( 'edit/toolbar' );
		
	}

	public function renderEditMain(
		?callable                                               $common_data_fields_renderer=null,
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $this->item);
		$view->setVar('listing', $this->listing);
		$view->setVar('tabs', $this->tabs);
		$view->setVar('form', $this->item->getEditForm());
		$view->setVar('common_data_fields_renderer', $common_data_fields_renderer);
		$view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $view->render( 'edit/main' );
		
	}
	
	public function renderEditImages(
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $this->item);
		$view->setVar('listing', $this->listing);
		$view->setVar('tabs', $this->tabs);
		$view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $view->render( 'edit/images' );
		
	}
	
	public function renderAdd(
		?callable $common_data_fields_renderer=null,
		?callable $eshop_data_fields_renderer=null
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $this->item);
		$view->setVar('tabs', $this->tabs);
		$view->setVar('form', $this->item->getAddForm());
		$view->setVar('common_data_fields_renderer', $common_data_fields_renderer);
		$view->setVar('eshop_data_fields_renderer', $eshop_data_fields_renderer);
		
		return $view->render( 'add' );
		
	}
	
	public function renderDeleteConfirm(
		string $message
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $this->item);
		$view->setVar('message', $message);
		
		return $view->render( 'delete/confirm' );
		
	}
	
	public function renderDeleteNotPossible(
		string $message,
		?callable $reason_renderer=null
	): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $this->item);
		$view->setVar('message', $message);
		$view->setVar('reason_renderer', $reason_renderer);
		
		return $view->render( 'delete/not-possible' );
	}
	
	public function renderShowName( int $id, null|Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface $item ): string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($id, $item) {
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