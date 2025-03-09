<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;


use Jet\Factory_MVC;
use JetApplication\Admin_Managers_MoneyRefund;
use JetApplication\EShopEntity_Basic;
use JetApplication\Order;
use JetApplication\MoneyRefund;


class Main extends Admin_Managers_MoneyRefund
{
	public const ADMIN_MAIN_PAGE = 'money-refunds';
	
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
		return new MoneyRefund();
	}
	
	public function showOrderMoneyRefunds( Order $order ) : string
	{
		$money_refunds= MoneyRefund::getByOrder( $order );
		if(!$money_refunds) {
			return '';
		}
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('money_refunds', $money_refunds);
		
		return $view->render('order-money-refunds');
	}
	
}