<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\OrderPersonalReceipt\DoPersonalReceipt;


use Jet\Factory_MVC;
use JetApplication\Admin_Managers_OrderPersonalReceipt;
use JetApplication\Context;
use JetApplication\EShopEntity_Basic;
use JetApplication\OrderPersonalReceipt;


class Main extends Admin_Managers_OrderPersonalReceipt
{
	public const ADMIN_MAIN_PAGE = 'do-personal-receipt';
	
	
	public function showDispatches( Context $context ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$dispatches = OrderPersonalReceipt::getListByContext( $context );
		
		$view->setVar('dispatches', $dispatches);
		
		return $view->render('dispatches');
	}
	
	public function showOrderPersonalReceiptStatus( OrderPersonalReceipt $dispatch ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('dispatch', $dispatch);
		
		return $view->render('status');
		
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new OrderPersonalReceipt();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Order Personal Receipt dispatch';
	}
	
}