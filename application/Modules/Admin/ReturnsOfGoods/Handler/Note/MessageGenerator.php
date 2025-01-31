<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\EShopEntity_Note_MessageGenerator;
use JetApplication\ReturnOfGoods;

abstract class Handler_Note_MessageGenerator extends EShopEntity_Note_MessageGenerator {
	protected MVC_View $view;
	protected ReturnOfGoods $return_of_goods;
	
	public function __construct( MVC_View $view, ReturnOfGoods $return_of_goods )
	{
		$view_dir = $view->getScriptsDir().'message/'.$return_of_goods->getEshopKey().'/'.$this->getKey().'/';
		if(!IO_Dir::exists($view_dir)) {
			IO_Dir::create( $view_dir );
		}
		
		$this->view = Factory_MVC::getViewInstance( $view_dir );
		
		$this->return_of_goods = $return_of_goods;
		$this->eshop = $return_of_goods->getEshop();
		
		$this->view->setVar('return_of_goods', $return_of_goods);
		$this->view->setVar('return_of_goods_number', $this->return_of_goods->getNumber());
		
	}
	
	
	/**
	 * @param MVC_View $view
	 * @param ReturnOfGoods $return_of_goods
	 *
	 * @return static[]
	 */
	public static function initGenerators( MVC_View $view, ReturnOfGoods $return_of_goods ) : array
	{
		$files = IO_Dir::getList( __DIR__.'/MessageGenerator', '*.php' );
		
		$generators = [];
		foreach($files as $name) {
			$class_name = Handler_Note_MessageGenerator::class.'_'.substr($name,0,-4);
			
			/**
			 * @var Handler_Note_MessageGenerator $generator
			 */
			$generator = new $class_name( $view, $return_of_goods );
			$generators[$generator->getKey()] = $generator;
		}
		
		return $generators;
	}
	
}