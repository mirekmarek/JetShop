<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Complaints;


use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\EShopEntity_Note_MessageGenerator;
use JetApplication\Complaint;

abstract class Handler_Note_MessageGenerator extends EShopEntity_Note_MessageGenerator {
	protected MVC_View $view;
	protected Complaint $complaint;
	
	public function __construct( MVC_View $view, Complaint $complaint )
	{
		$view_dir = $view->getScriptsDir().'message/'.$complaint->getEshopKey().'/'.$this->getKey().'/';
		if(!IO_Dir::exists($view_dir)) {
			IO_Dir::create( $view_dir );
		}
		
		$this->view = Factory_MVC::getViewInstance( $view_dir );
		
		$this->complaint = $complaint;
		$this->eshop = $complaint->getEshop();
		
		$this->view->setVar('complaint', $complaint);
		$this->view->setVar('complaint_number', $this->complaint->getNumber());
		
	}
	
	
	/**
	 * @param MVC_View $view
	 * @param Complaint $complaint
	 *
	 * @return static[]
	 */
	public static function initGenerators( MVC_View $view, Complaint $complaint ) : array
	{
		$files = IO_Dir::getList( __DIR__.'/MessageGenerator', '*.php' );
		
		$generators = [];
		foreach($files as $name) {
			$class_name = Handler_Note_MessageGenerator::class.'_'.substr($name,0,-4);
			
			/**
			 * @var Handler_Note_MessageGenerator $generator
			 */
			$generator = new $class_name( $view, $complaint );
			$generators[$generator->getKey()] = $generator;
		}
		
		return $generators;
	}
	
}