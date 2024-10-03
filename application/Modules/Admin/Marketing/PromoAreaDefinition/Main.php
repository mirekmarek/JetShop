<?php
namespace JetApplicationModule\Admin\Marketing\PromoAreaDefinition;

use JetApplication\Admin_EntityManager_Common_Interface;
use JetApplication\Admin_EntityManager_Common_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Entity_Common;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Common_Interface
{
	use Admin_EntityManager_Common_Trait;

	public const ADMIN_MAIN_PAGE = 'promo-areas-definition';

	public const ACTION_GET = 'get_promo_area_definition';
	public const ACTION_ADD = 'add_promo_area_definition';
	public const ACTION_UPDATE = 'update_promo_area_definition';
	public const ACTION_DELETE = 'delete_promo_area_definition';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new PromoAreaDefinition();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Promo area definition';
	}

}