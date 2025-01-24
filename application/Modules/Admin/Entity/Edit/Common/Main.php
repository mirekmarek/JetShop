<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Entity\Edit\Common;

use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Translator;
use Jet\UI_tabs;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Managers_Entity_Edit_Common;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_Basic;
use JetApplication\Entity_Common;
use Closure;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Entity_Edit_Common
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
	
	public function renderEditImages(
		?callable                                               $toolbar_renderer=null
	) : string
	{
		$this->view->setVar('item', $this->item);
		$this->view->setVar('listing', $this->listing);
		$this->view->setVar('tabs', $this->tabs);
		$this->view->setVar('toolbar_renderer', $toolbar_renderer);
		
		return $this->render( 'edit/images' );
		
	}
	
	public function renderAdd(
		?callable $common_data_fields_renderer=null,
		?callable $eshop_data_fields_renderer=null
	) : string
	{
		$this->view->setVar('item', $this->item);
		$this->view->setVar('tabs', $this->tabs);
		$this->view->setVar('form', $this->item->getAddForm());
		$this->view->setVar('common_data_fields_renderer', $common_data_fields_renderer);
		$this->view->setVar('eshop_data_fields_renderer', $eshop_data_fields_renderer);
		
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
	
	public function renderShowName( int $id, null|Entity_Common|Admin_Entity_Interface $item ): string
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
	
	public function renderEntityActivation(
		Entity_Common|Admin_Entity_Common_Interface $entity,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null
	) : string
	{
		$this->view->setVar('entity', $entity);
		
		if(!$editable) {
			return $this->render( 'entity-activation/readonly' );
		}
		
		if(!$deactivate_url_creator) {
			$deactivate_url_creator = function () : string {
				return Http_Request::currentURI(['deactivate_entity'=>1]);
			};
		}
		if(!$activate_url_creator) {
			$activate_url_creator = function () : string {
				return Http_Request::currentURI(['activate_entity'=>1]);
			};
		}
		
		$this->view->setVar('deactivate_url', $deactivate_url_creator() );
		$this->view->setVar('activate_url', $activate_url_creator() );
		
		return $this->render( 'entity-activation/editable' );
		
		
	}
	
	public function renderEntityFormCommonFields( Form $form ) : string
	{
		$this->view->setVar('form', $form);
		
		return $this->render( 'entity-form-common-fields' );
		
	}
	
	
}