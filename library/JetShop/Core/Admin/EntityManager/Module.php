<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Auth;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Auth_Administrator_Role;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivation_Interface;

abstract class Core_Admin_EntityManager_Module extends Application_Module {
	
	public const ADMIN_MAIN_PAGE = null;
	
	public const ACTION_GET = 'get';
	public const ACTION_ADD = 'add';
	public const ACTION_UPDATE = 'update';
	public const ACTION_DELETE = 'delete';
	
	
	public static function getAdminMainPageID() : string
	{
		return static::ADMIN_MAIN_PAGE;
	}
	
	public static function getAdminMainPage() : MVC_Page_Interface
	{
		return MVC::getPage( static::getAdminMainPageID() );
	}
	
	public static function getEditUrl( int|EShopEntity_Basic $id_or_item, array $get_params=[] ) : string
	{
		if($id_or_item instanceof EShopEntity_Basic) {
			$id = $id_or_item->getId();
		} else {
			$id = $id_or_item;
		}
		
		$page = static::getAdminMainPage();
		
		$get_params['id'] = $id;
		
		return $page->getURL([], $get_params);
		
	}
	
	public function renderItemName( int|EShopEntity_Basic $id_or_item ): string
	{
		if(is_int($id_or_item)) {
			$id = $id_or_item;
			$item = static::getEntityInstance()::get( $id_or_item );
		} else {
			$item = $id_or_item;
			$id = $item->getId();
		}
		
		return Admin_Managers::EntityEdit()->renderItemName( $id, $item );
	}
	
	protected static function getModuleName() : string
	{
		$module_name = substr( get_called_class(), 21, -5 );
		$module_name = str_replace('\\', '.', $module_name);
		
		return $module_name;
	}
	
	public static function getCurrentUserCanEdit() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::getModuleName().':'.static::ACTION_UPDATE );
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::getModuleName().':'.static::ACTION_ADD );
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::getModuleName().':'.static::ACTION_DELETE );
	}
	
	public function renderActiveState( EShopEntity_HasActivation_Interface $item ) : string
	{
		return Admin_Managers::EntityEdit()->renderActiveState( $item );
	}
	
	abstract public static function getEntityInstance(): EShopEntity_Basic|EShopEntity_Admin_Interface;

	public static function getEntityNameReadable() : string
	{
		return static::getEntityInstance()::getEntityDefinition()->getEntityNameReadable();
	}
}