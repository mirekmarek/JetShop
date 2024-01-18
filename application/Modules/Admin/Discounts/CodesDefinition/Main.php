<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Common_Manager_Interface;
use JetApplication\Admin_Entity_Common_Manager_Trait;
use JetApplication\Entity_Basic;
use JetApplication\Admin_Entity_Common_Interface;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_Common_Manager_Interface
{
	use Admin_Entity_Common_Manager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'discounts-codes-definition';

	public const ACTION_GET = 'get_discounts_code';
	public const ACTION_ADD = 'add_discounts_code';
	public const ACTION_UPDATE = 'update_discounts_code';
	public const ACTION_DELETE = 'delete_discounts_code';
	
	public static function getName( int $id ): string
	{
		return DiscountsCode::get($id)?->getAdminTitle();
	}
	
	public static function showName( int $id ): string
	{
		$title = DiscountsCode::get($id)?->getAdminTitle();
		
		return '<a href="'.static::getEditUrl($id).'">'.$title.'</a>';
	}
	
	public static function showActiveState( int $id ): string
	{
		return '';
	}
	
	public static function getEntityInstance(): Entity_Basic|Admin_Entity_Common_Interface
	{
		return new DiscountsCode();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'customer';
	}


}