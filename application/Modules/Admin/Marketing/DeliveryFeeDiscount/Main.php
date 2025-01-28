<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\DeliveryFeeDiscount;

use Jet\Application_Module;
use JetApplication\Admin_Managers_MarketingDeliveryFeeDiscounts;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Entity_Basic;
use JetApplication\Marketing_DeliveryFeeDiscount;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_MarketingDeliveryFeeDiscounts
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'delivery-fee-discounts';

	public const ACTION_GET = 'get_delivery_fee_discount';
	public const ACTION_ADD = 'add_delivery_fee_discount';
	public const ACTION_UPDATE = 'update_delivery_fee_discount';
	public const ACTION_DELETE = 'delete_delivery_fee_discount';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Marketing_DeliveryFeeDiscount();
	}

}