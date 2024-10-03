<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Entity\Edit\Simple;

use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\MVC_View;
use Jet\UI_tabs;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Managers_Entity_Edit_Simple;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Entity_Edit_Simple
{
	protected Entity_Basic|Admin_Entity_Interface $item;
	protected ?Admin_Managers_Entity_Listing $listing = null;
	protected ?UI_tabs $tabs = null;
	protected MVC_View $view;
	
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	protected function render( $script ) : string
	{
		return $this->view->render($script);
	}
	
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
		$this->view->setVar('item', $this->item);
		$this->view->setVar('form', $form);
		$this->view->setVar('listing', $this->listing);

		$this->view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $this->render( 'edit/toolbar' );
		
	}

	public function renderEditMain(
		?callable                                               $common_data_fields_renderer=null,
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$this->view->setVar('item', $this->item);
		$this->view->setVar('listing', $this->listing);
		$this->view->setVar('tabs', $this->tabs);
		$this->view->setVar('form', $this->item->getEditForm());
		$this->view->setVar('common_data_fields_renderer', $common_data_fields_renderer);
		$this->view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $this->render( 'edit/main' );
		
	}

	public function renderAdd(
		?callable $common_data_fields_renderer=null,
		?callable $shop_data_fields_renderer=null
	) : string
	{
		$this->view->setVar('item', $this->item);
		$this->view->setVar('tabs', $this->tabs);
		$this->view->setVar('form', $this->item->getAddForm());
		$this->view->setVar('common_data_fields_renderer', $common_data_fields_renderer);
		$this->view->setVar('shop_data_fields_renderer', $shop_data_fields_renderer);
		
		return $this->render( 'add' );
		
	}
	
	public function renderDeleteConfirm(
		string $message
	) : string
	{
		$this->view->setVar('item', $this->item);
		$this->view->setVar('message', $message);
		
		return $this->render( 'delete/confirm' );
		
	}
	
	public function renderDeleteNotPossible(
		string $message,
		?callable $reason_renderer=null
	): string
	{
		$this->view->setVar('item', $this->item);
		$this->view->setVar('message', $message);
		$this->view->setVar('reason_renderer', $reason_renderer);
		
		return $this->render( 'delete/not-possible' );
	}
	
	
}