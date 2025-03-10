<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Autoloader;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\AdministratorSignatures;
use JetApplication\EShop;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Note_MessageGenerator;

abstract class Core_EShopEntity_Note_MessageGenerator {
	
	public const KEY = null;
	
	protected MVC_View $view;
	protected EShop $eshop;
	
	public function getKey() : string
	{
		return static::KEY;
	}
	
	abstract public function getTitle() : string;
	
	abstract public function generateSubject() : string;
	
	public function renderSubject() : string
	{
		return trim($this->view->render('subject'));
	}
	
	abstract public function generateText() : string;
	
	public function renderText( bool $append_signature) : string
	{
		$text = $this->view->render('text');
		
		if($append_signature) {
			$text .= "\n\n".AdministratorSignatures::getSignature( $this->eshop );
		}
		
		return $text;
	}
	
	/**
	 * @param MVC_View $view
	 * @param EShopEntity_Basic $item
	 *
	 * @return static[]
	 */
	public static function initGenerators( MVC_View $view, EShopEntity_Basic $item ) : array
	{
		$files = IO_Dir::getList( dirname(Autoloader::getScriptPath(static::class)).'/MessageGenerator', '*.php' );
		
		$generators = [];
		foreach($files as $name) {
			$class_name = static::class.'_'.substr($name,0,-4);
			
			/**
			 * @var EShopEntity_Note_MessageGenerator $generator
			 */
			$generator = new $class_name( $view, $item );
			$generators[$generator->getKey()] = $generator;
		}
		
		return $generators;
	}
	

}