<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\DeliveryFeeDiscount;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Managers_MarketingDeliveryFeeDiscounts;
use JetApplication\Admin_EntityManager_Marketing_Trait;
use JetApplication\Entity_Marketing;
use JetApplication\Marketing_DeliveryFeeDiscount;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_MarketingDeliveryFeeDiscounts
{
	use Admin_EntityManager_Marketing_Trait;
	
	public const ADMIN_MAIN_PAGE = 'delivery-fee-discounts';

	public const ACTION_GET = 'get_delivery_fee_discount';
	public const ACTION_ADD = 'add_delivery_fee_discount';
	public const ACTION_UPDATE = 'update_delivery_fee_discount';
	public const ACTION_DELETE = 'delete_delivery_fee_discount';
	
	
	public static function getEntityInstance(): Admin_Entity_Marketing_Interface|Entity_Marketing
	{
		return new Marketing_DeliveryFeeDiscount();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Marketing - Delivery Fee Discount';
	}


}