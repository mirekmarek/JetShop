<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use Jet\Factory_MVC;
use JetApplication\Admin_Managers_Complaint;
use JetApplication\EShopEntity_Basic;
use JetApplication\Order;
use JetApplication\Complaint;
use JetApplication\PDF_TemplateProvider;


class Main extends Admin_Managers_Complaint implements PDF_TemplateProvider
{
	public const ADMIN_MAIN_PAGE = 'complaints';
	
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
		return new Complaint();
	}
	
	public function showOrderComplaints( Order $order ) : void
	{
		$complaints= Complaint::getByOrder( $order );
		if(!$complaints) {
			return;
		}
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('complaints', $complaints);
		
		echo $view->render('order-complaints');
	}
	
	public function getPDFTemplates(): array
	{
		return [
			new PDFTemplate_ServiceReport()
		];
	}
}