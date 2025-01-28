<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;

use Jet\Application_Module;
use Jet\Factory_MVC;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_ReturnOfGoods;
use JetApplication\Entity_Basic;
use JetApplication\Order;
use JetApplication\ReturnOfGoods;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ReturnOfGoods
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'returns-of-goods';

	public const ACTION_GET = 'get_return_of_goods';
	public const ACTION_UPDATE = 'update_return_of_goods';
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new ReturnOfGoods();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Return of goods';
	}
	
	
	
	public function showReturnOfGoodsStatus( ReturnOfGoods $return_of_goods ): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('return_of_goods', $return_of_goods);
		
		return $view->render('return_of_goods_status');
	}
	
	public function showOrderReturnsOfGoods( Order $order ): void
	{
		$returns= ReturnOfGoods::getByOrder( $order );
		if(!$returns) {
			return;
		}
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('returns', $returns);
		
		echo $view->render('order-returns');
	}
	
}