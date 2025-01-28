<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\PromoAreas;

use Jet\Application_Module;
use JetApplication\Admin_Managers_MarketingPromoAreas;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Entity_Basic;
use JetApplication\Marketing_PromoArea;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_MarketingPromoAreas
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'promo-areas';

	public const ACTION_GET = 'get_promo_area';
	public const ACTION_ADD = 'add_promo_area';
	public const ACTION_UPDATE = 'update_promo_area';
	public const ACTION_DELETE = 'delete_promo_area';
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Marketing_PromoArea();
	}
}