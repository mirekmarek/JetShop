<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\Http_Headers;
use Jet\Http_Request;

use JetApplication\Admin_Managers_Entity_Edit_Common;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Admin_EntityManager_Common_Interface;
use JetApplication\Entity_Common;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Managers;


/**
 *
 */
abstract class Core_Admin_EntityManager_Common_Controller extends Admin_EntityManager_Controller
{
	
	/**
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 * @var Application_Module|null|Admin_EntityManager_Common_Interface
	 */
	protected ?Application_Module $module = null;
	
	/**
	 * @var Entity_Common|Admin_Entity_Common_Interface|null
	 */
	protected mixed $current_item = null;
	
	
	public function edit_main_handleActivation() : void
	{
		$GET = Http_Request::GET();
		
		if($GET->exists('deactivate_entity')) {
			$this->current_item->deactivate();
			
			Http_Headers::reload(unset_GET_params: ['deactivate_entity']);
		}
		
		if($GET->exists('activate_entity')) {
			$this->current_item->activate();
			
			Http_Headers::reload(unset_GET_params: ['activate_entity']);
		}
		
	}
	
	
	public function getEditorManager(): Admin_Managers_Entity_Edit_Common|Application_Module
	{
		if(!$this->editor_manager) {
			$this->editor_manager = Admin_Managers::EntityEdit_Common();
			$this->editor_manager->init(
				$this->current_item,
				$this->getListing(),
				$this->tabs
			);
		}
		
		return $this->editor_manager;
	}
	

}