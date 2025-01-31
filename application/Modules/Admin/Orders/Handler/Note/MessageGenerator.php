<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Orders;


use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\EShopEntity_Note_MessageGenerator;
use JetApplication\Order;

abstract class Handler_Note_MessageGenerator extends EShopEntity_Note_MessageGenerator {
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
		
		$this->view->setVar('order', $order);
		$this->view->setVar('order_number', $this->order->getNumber());
		
	}
	
	
	/**
	 * @param MVC_View $view
	 * @param Order $order
	 *
	 * @return static[]
	 */
	public static function initGenerators( MVC_View $view, Order $order ) : array
	{
		$files = IO_Dir::getList( __DIR__.'/MessageGenerator', '*.php' );
		
		$generators = [];
		foreach($files as $name) {
			$class_name = Handler_Note_MessageGenerator::class.'_'.substr($name,0,-4);
			
			/**
			 * @var Handler_Note_MessageGenerator $generator
			 */
			$generator = new $class_name( $view, $order );
			$generators[$generator->getKey()] = $generator;
		}
		
		return $generators;
	}
	
}