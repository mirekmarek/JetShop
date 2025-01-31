<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Complaints;


use Jet\Factory_MVC;
use JetApplication\Admin_Managers_Complaint;
use JetApplication\EShopEntity_Basic;
use JetApplication\Order;
use JetApplication\Complaint;


class Main extends Admin_Managers_Complaint
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
	
	public static function getEntityNameReadable(): string
	{
		return 'Complaint';
	}
	
	public function showComplaintStatus( Complaint $complaint ): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('complaint', $complaint);
		
		return $view->render('complaint_status');
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

}