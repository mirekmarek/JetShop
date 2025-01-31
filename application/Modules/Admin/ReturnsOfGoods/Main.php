<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\ReturnsOfGoods;


use Jet\Factory_MVC;
use JetApplication\Admin_Managers_ReturnOfGoods;
use JetApplication\EShopEntity_Basic;
use JetApplication\Order;
use JetApplication\ReturnOfGoods;


class Main extends Admin_Managers_ReturnOfGoods
{
	public const ADMIN_MAIN_PAGE = 'returns-of-goods';
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new ReturnOfGoods();
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