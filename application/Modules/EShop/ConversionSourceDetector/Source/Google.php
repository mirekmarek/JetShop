<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\EShop\ConversionSourceDetector;


use JetApplication\Marketing_ConversionSourceDetector_Source;

class Source_Google extends Marketing_ConversionSourceDetector_Source
{
	
	public function getName(): string
	{
		return 'Google';
	}
	
	public function performDetection(): void
	{
		if($this->detected===null) {
			if(
				str_contains( $this->referer_host, 'google.com' )
			) {
				$this->setIsDetected();
			} else {
				$this->setIsNotDetected();
			}
		}
	}
}