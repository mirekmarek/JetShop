<?php
namespace JetApplicationModule\Admin\Marketing\AutoOffers;
use JetApplication\Admin_Managers_Marketing_AutoOffers;
use JetApplication\Admin_EntityManager_Trait;
use Jet\Application_Module;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_AutoOffer;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Marketing_AutoOffers
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'auto_offers';

	public const ACTION_GET = 'get_auto_offer';
	public const ACTION_ADD = 'add_auto_offer';
	public const ACTION_UPDATE = 'update_auto_offer';
	public const ACTION_DELETE = 'delete_auto_offer';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_AutoOffer();
	}
}