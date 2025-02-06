<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\InvoicesInAdvance;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Managers_InvoiceInAdvance;
use JetApplication\EShopEntity_Basic;
use JetApplication\InvoiceInAdvance;
use JetApplication\Order;


class Main extends Admin_Managers_InvoiceInAdvance
{
	public const ADMIN_MAIN_PAGE = 'invoices-in-advance';
	
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
		return new InvoiceInAdvance();
	}
	
	public function showOrderInvoices( Order $order ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($order) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar( 'invoices', InvoiceInAdvance::getListByOrder( $order ) );
				
				return $view->render('order-invoices');
			}
		);
	}
	
}