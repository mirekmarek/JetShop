<?php
/**
 *
 */

namespace JetShop;

use Closure;
use JetApplication\EMail_Template_Property_Param;

abstract class Core_EMail_Template_Property {

	protected string $name = '';
	
	protected string $description = '';
	
	protected Closure $property_value_creator;
	
	/**
	 * @var EMail_Template_Property_Param[]
	 */
	protected array $params = [];
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getInstructionTemplate(): string
	{
		if(!$this->params) {
			return '%'.$this->name.'%';
		}
		
		$params = [];
		foreach($this->params as $param) {
			$params[] = $param->getInstructionTemplate();
		}
		$params = implode(',', $params);
		
		return '%'.$this->name.'('.$params.')%';
	}
	
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	public function getPropertyValueCreator(): Closure
	{
		return $this->property_value_creator;
	}
	
	public function setPropertyValueCreator( Closure $property_value_creator ): void
	{
		$this->property_value_creator = $property_value_creator;
	}
	
	public function addParam( string $type, string $name, string $description='' ) : EMail_Template_Property_Param
	{
		$param = new EMail_Template_Property_Param();
		$param->setType( $type );
		$param->setName( $name );
		$param->setDescription( $description );
		
		$this->params[ $name ] = $param;
		
		return $param;
	}
	
	public function createRegExp() : string
	{
		$reg_exp = '/%'.$this->name;
		
		if($this->params) {
			$param_reg_exp = [];
			foreach($this->params as $param) {
				$param_reg_exp[] = $param->createRegExp();
			}
			$reg_exp .= '\(';
			$reg_exp .= implode(',', $param_reg_exp);
			$reg_exp .= '\)';
		}
		
		$reg_exp .= '%/';
		
		return $reg_exp;
	}
	
	public function processText( string &$text, mixed $block_item=null  ) : void
	{
		if(!preg_match($this->createRegExp(), $text, $matches)) {
			return;
		}
		
		$creator = $this->getPropertyValueCreator();
		

		$params = [];
		$i = 1;
		foreach($this->params as $param) {
			$params[$param->getName()] = $param->checkType($matches[$i]);
			$i++;
		}
		
		if($block_item) {
			$value = $creator( $block_item, $params );
		} else {
			$value = $creator( $params );
		}
		
		$text = str_replace( $matches[0], $value, $text );
	}
	
}