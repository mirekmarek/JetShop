<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderDispatch\DoDispatch;


use Jet\Factory_MVC;
use JetApplication\Admin_Managers_OrderDispatch;
use JetApplication\Context;
use JetApplication\EShopEntity_Basic;
use JetApplication\OrderDispatch;


class Main extends Admin_Managers_OrderDispatch
{
	public const ADMIN_MAIN_PAGE = 'do-dispatch';
	
	public function showDispatches( Context $context ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$dispatches = OrderDispatch::getListByContext( $context );
		
		$view->setVar('dispatches', $dispatches);
		
		return $view->render('dispatches');
	}
	
	public function showOrderDispatchStatus( OrderDispatch $dispatch ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('dispatch', $dispatch);
		
		return $view->render('status');
		
	}
	
	public function showRecipient( OrderDispatch $dispatch ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('dispatch', $dispatch);
		
		return $view->render('recipient');
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new OrderDispatch();
	}

}