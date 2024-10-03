<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\ConversionSourceDetector;

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