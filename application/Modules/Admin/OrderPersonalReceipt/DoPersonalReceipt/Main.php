<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderPersonalReceipt\DoPersonalReceipt;


use Jet\Factory_MVC;
use JetApplication\Application_Service_Admin_OrderPersonalReceipt;
use JetApplication\Context;
use JetApplication\EShopEntity_Basic;
use JetApplication\OrderPersonalReceipt;


class Main extends Application_Service_Admin_OrderPersonalReceipt
{
	public const ADMIN_MAIN_PAGE = 'do-personal-receipt';
	
	
	public function showDispatches( Context $context ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$dispatches = OrderPersonalReceipt::getListByContext( $context );
		
		$view->setVar('dispatches', $dispatches);
		
		return $view->render('dispatches');
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new OrderPersonalReceipt();
	}
	
}