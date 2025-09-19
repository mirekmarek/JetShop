<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ProformaInvoices;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Application_Service_Admin_ProformaInvoice;
use JetApplication\EShopEntity_Basic;
use JetApplication\ProformaInvoice;
use JetApplication\Order;


class Main extends Application_Service_Admin_ProformaInvoice
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
		return new ProformaInvoice();
	}
	
	public function showOrderInvoices( Order $order ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($order) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar( 'invoices', ProformaInvoice::getListByOrder( $order ) );
				
				return $view->render('order-invoices');
			}
		);
	}
	
}