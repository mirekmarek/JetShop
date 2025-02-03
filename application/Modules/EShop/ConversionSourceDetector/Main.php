<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ConversionSourceDetector;

use Jet\IO_Dir;
use JetApplication\Marketing_ConversionSourceDetector_Manager;
use JetApplication\Marketing_ConversionSourceDetector_Source;


class Main extends Marketing_ConversionSourceDetector_Manager
{
	/**
	 * @var Marketing_ConversionSourceDetector_Source[]|null
	 */
	protected ?array $sources = null;
	
	/**
	 * @return Marketing_ConversionSourceDetector_Source[]
	 */
	public function getAllSources(): array
	{
		if($this->sources===null) {
			$this->sources = [];
			
			$dir = __DIR__.'/Source/';
			$sources = IO_Dir::getFilesList( $dir, '*.php' );
			
			foreach($sources as $file_name) {
				$class_name = $this->module_manifest->getNamespace().'Source_'.pathinfo($file_name, PATHINFO_FILENAME);
				/**
				 * @var Marketing_ConversionSourceDetector_Source $source
				 */
				$source = new $class_name();

				$this->sources[$source->getName()] = $source;
				
			}
		}

		return $this->sources;
	}
	
	public function performDetection(): void
	{
		foreach($this->getAllSources() as $source) {
			$source->performDetection();
		}
	}
	
	public function reset(): void
	{
		foreach($this->getAllSources() as $source) {
			$source->reset();
		}
	}
}