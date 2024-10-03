<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Admin_Managers_Entity_Edit_WithShopRelation;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Admin_EntityManager_WithShopRelation_Interface;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_Managers;

/**
 *
 */
abstract class Core_Admin_EntityManager_WithShopRelation_Controller extends Admin_EntityManager_Controller
{
	
	/**
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 * @var Application_Module|null|Admin_EntityManager_WithShopRelation_Interface
	 */
	protected ?Application_Module $module = null;
	
	/**
	 * @var Entity_WithShopRelation|Admin_Entity_WithShopRelation_Interface|null
	 */
	protected mixed $current_item = null;
	
	public function getEditorManager(): Admin_Managers_Entity_Edit_WithShopRelation|Application_Module
	{
		if(!$this->editor_manager) {
			$this->editor_manager = Admin_Managers::EntityEdit_WithShopRelation();
			$this->editor_manager->init(
				$this->current_item,
				$this->getListing(),
				$this->tabs
			);
		}
		
		return $this->editor_manager;
	}
	
}