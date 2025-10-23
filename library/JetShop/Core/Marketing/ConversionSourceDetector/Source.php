<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use Jet\Session;

abstract class Core_Marketing_ConversionSourceDetector_Source
{
	protected ?bool $detected = null;
	protected ?Session $session = null;
	protected string $referer_host;
	
	public function __construct()
	{
		$this->detected = $this->getSession()->getValue($this->getName(), null);
		$this->referer_host = $this->getRefererHost();
		
	}
	
	abstract public function getName() : string;
	
	abstract public function performDetection() : void;
	
	public function isDetected() : ?bool
	{
		return $this->detected;
	}
	
	public function getAccessDateTime() : ?Data_DateTime
	{
		return $this->getSession()->getValue($this->getName().'_date_time');
	}
	
	protected function getSession() : Session
	{
		if(!$this->session) {
			$this->session = new Session('conversion_source_detector');
		}
		
		return $this->session;
	}
	
	public function reset() : void
	{
		$this->getSession()->setValue($this->getName(), null);
		$this->getSession()->setValue($this->getName().'_date_time', null);
		$this->detected = null;
	}
	
	
	protected function setIsDetected() : void
	{
		$this->getSession()->setValue($this->getName(), true);
		$this->getSession()->setValue($this->getName().'_date_time', Data_DateTime::now());
		$this->detected = true;
	}
	
	protected function setIsNotDetected() : void
	{
		$this->getSession()->setValue($this->getName(), false);
		$this->getSession()->setValue($this->getName().'_date_time', null);
		$this->detected = false;
	}
	
	protected function checkUTMValue( $key, $value ) : bool
	{
		if(!isset($this->UTM_values[$key])){
			return false;
		}
		
		return $this->UTM_values[$key]==$value;
	}
	
	private function getRefererHost() : string
	{
		if(empty( $_SERVER['HTTP_REFERER'] )) {
			return '';
		}
		
		$parsed_referer = parse_url( $_SERVER['HTTP_REFERER'] );
		
		$host = $parsed_referer['host']??'';
		
		if($host==$_SERVER['HTTP_HOST']) {
			return '';
		}
		
		return $host;
	}
}