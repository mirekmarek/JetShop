<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\EShopEntity_Note_MessageGenerator;
use JetApplication\Order;

abstract class Plugin_Note_MessageGenerator extends EShopEntity_Note_MessageGenerator {
	protected MVC_View $view;
	protected Order $order;
	
	public function __construct( MVC_View $view, Order $order )
	{
		$view_dir = $view->getScriptsDir().'message/'.$order->getEshopKey().'/'.$this->getKey().'/';
		if(!IO_Dir::exists($view_dir)) {
			IO_Dir::create( $view_dir );
		}
		
		$this->view = Factory_MVC::getViewInstance( $view_dir );
		
		$this->order = $order;
		$this->eshop = $order->getEshop();
		
		$this->view->setVar('item', $order);
		$this->view->setVar('order_number', $this->order->getNumber());
		
	}
}