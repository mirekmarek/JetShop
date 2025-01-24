<?php
namespace JetApplicationModule\Admin\Marketing\AutoOffers;
use JetApplication\Admin_Managers_MarketingAutoOffers;
use JetApplication\Admin_EntityManager_Marketing_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Entity_Marketing;
use JetApplication\Marketing_AutoOffer;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_MarketingAutoOffers
{
	use Admin_EntityManager_Marketing_Trait;

	public const ADMIN_MAIN_PAGE = 'auto_offers';

	public const ACTION_GET = 'get_auto_offer';
	public const ACTION_ADD = 'add_auto_offer';
	public const ACTION_UPDATE = 'update_auto_offer';
	public const ACTION_DELETE = 'delete_auto_offer';
	
	
	public static function getEntityInstance(): Entity_Marketing|Admin_Entity_Marketing_Interface
	{
		return new Marketing_AutoOffer();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Marketing - Automatic offer';
	}

}