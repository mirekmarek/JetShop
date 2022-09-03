<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Catalog\PropertyGroups;

use Jet\Application_Module;
use Jet\MVC;
use JetShop\Admin_Module_Trait;
use JetShop\PropertyGroup_ManageModuleInterface;

/**
 *
 */
class Main extends Application_Module implements PropertyGroup_ManageModuleInterface
{
	use Admin_Module_Trait;
	
	const ADMIN_MAIN_PAGE = 'property-group';

	const ACTION_GET_PROPERTY_GROUP = 'get_property_group';
	const ACTION_ADD_PROPERTY_GROUP = 'add_property_group';
	const ACTION_UPDATE_PROPERTY_GROUP = 'update_property_group';
	const ACTION_DELETE_PROPERTY_GROUP = 'delete_property_group';
	
	
	public function getPropertyGroupEditUrl( int $id ): string
	{
		return $this->getEditUrl(
			static::ACTION_GET_PROPERTY_GROUP,
			static::ACTION_UPDATE_PROPERTY_GROUP,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}
	
	public function getPropertyGroupSelectWhispererUrl( $only_active = false ): string
	{
		$page = MVC::getPage( static::ADMIN_MAIN_PAGE );
		if(!$page) {
			return '';
		}
		
		return $page->getURL([], [
			'only_active' => $only_active ? 1:0
		]);
	}
}