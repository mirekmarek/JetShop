<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Catalog\Properties;

use Jet\Application_Module;
use Jet\MVC;
use JetShop\Admin_Module_Trait;
use JetShop\Property_ManageModuleInterface;

/**
 *
 */
class Main extends Application_Module implements Property_ManageModuleInterface
{
	use Admin_Module_Trait;
	
	const ADMIN_MAIN_PAGE = 'properties';

	const ACTION_GET_PROPERTY = 'get_property';
	const ACTION_ADD_PROPERTY = 'add_property';
	const ACTION_UPDATE_PROPERTY = 'update_property';
	const ACTION_DELETE_PROPERTY = 'delete_property';
	
	
	public function getPropertyEditUrl( int $id ): string
	{
		return $this->getEditUrl(
			static::ACTION_GET_PROPERTY,
			static::ACTION_UPDATE_PROPERTY,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}
	
	public function getPropertySelectWhispererUrl( string $only_type, bool $only_active = false ): string
	{
		$page = MVC::getPage( static::ADMIN_MAIN_PAGE );
		if(!$page) {
			return '';
		}
		
		return $page->getURL([], [
			'only_type' => $only_type,
			'only_active' => $only_active ? 1:0
		]);
	}
}