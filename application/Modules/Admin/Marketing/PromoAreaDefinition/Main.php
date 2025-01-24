<?php
namespace JetApplicationModule\Admin\Marketing\PromoAreaDefinition;

use JetApplication\Admin_Managers_MarketingPromoAreaDefinitions;
use JetApplication\Admin_EntityManager_Common_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Entity_Common;
use JetApplication\Marketing_PromoAreaDefinition;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_MarketingPromoAreaDefinitions
{
	use Admin_EntityManager_Common_Trait;

	public const ADMIN_MAIN_PAGE = 'promo-areas-definition';

	public const ACTION_GET = 'get_promo_area_definition';
	public const ACTION_ADD = 'add_promo_area_definition';
	public const ACTION_UPDATE = 'update_promo_area_definition';
	public const ACTION_DELETE = 'delete_promo_area_definition';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new Marketing_PromoAreaDefinition();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Marketing - Promo area definition';
	}

}