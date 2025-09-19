<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\MagicTags;

use Jet\IO_Dir;
use Jet\MVC_Layout;
use Jet\Translator;
use JetApplication\Content_MagicTag;
use JetApplication\Application_Service_EShop_MagicTags;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;

class Main extends Application_Service_EShop_MagicTags implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected ?array $classes_list = null;
	
	protected function getClassesList() : array
	{
		if($this->classes_list===null) {
			$this->classes_list = [];
			
			$list = IO_Dir::getFilesList( __DIR__.'/MagicTag/', '*.php' );
			
			foreach($list as $file) {
				$class_name = MagicTag::class.'_'.substr( $file, 0, -4 );
				
				$this->classes_list[] = $class_name;
			}
		}
		
		return $this->classes_list;
	}
	
	
	/**
	 * @return Content_MagicTag[]
	 */
	public function getList() : array
	{
		return Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() : array {
				$result = [];
				foreach($this->getClassesList() as $class_name) {
					/**
					 * @var Content_MagicTag $mg
					 */
					$mg = new $class_name();
					$result[$mg->getId()] = $mg;
				}
				
				uasort( $result, function( MagicTag $a, MagicTag $b ) {
					return strcmp( $a->getTitle(), $b->getTitle() );
				} );
				
				return $result;
			}
		);
	}
	
	
	public function init(): void
	{
		$layout = MVC_Layout::getCurrentLayout();
		$view = $this->getView();
		
		foreach($this->getClassesList() as $class_name) {
			$layout->addOutputPostprocessor( new $class_name( $layout, $view ) );
		}
	}
	
	public function processText( string $text ) : string
	{
		$view = $this->getView();
		
		foreach($this->getClassesList() as $class_name) {
			/**
			 * @var Content_MagicTag $mg
			 */
			$mg = new $class_name( view: $view );
			
			$text = $mg->process( $text );
		}
	
		return $text;
	}
}