<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;

use JetApplication\Admin_Managers_Entity_Edit_Simple;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Admin_EntityManager_Simple_Interface;
use JetApplication\Entity_Simple;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Managers;


/**
 *
 */
abstract class Core_Admin_EntityManager_Simple_Controller extends Admin_EntityManager_Controller
{
	
	/**
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 * @var Application_Module|null|Admin_EntityManager_Simple_Interface
	 */
	protected ?Application_Module $module = null;
	
	/**
	 * @var Entity_Simple|Admin_Entity_Simple_Interface|null
	 */
	protected mixed $current_item = null;
	
	
	public function getEditorManager(): Admin_Managers_Entity_Edit_Simple|Application_Module
	{
		if(!$this->editor_manager) {
			$this->editor_manager = Admin_Managers::EntityEdit_Simple();
			$this->editor_manager->init(
				$this->current_item,
				$this->getListing(),
				$this->tabs
			);
		}
		
		return $this->editor_manager;
	}
	

}