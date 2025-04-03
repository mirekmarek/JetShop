<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Closure;

abstract class Core_Template_Condition {
	
	protected string $name = '';
	
	protected string $description = '';
	
	protected Closure $condition_evaluator;
	
	
	public function getName(): string
	{
		return $this->name;
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
	
	
	public function getConditionEvaluator(): Closure
	{
		return $this->condition_evaluator;
	}
	
	public function setConditionEvaluator( Closure $condition_evaluator ): void
	{
		$this->condition_evaluator = $condition_evaluator;
	}
	
	
	
	public function process( string &$text, mixed $block_item=null    ) : void
	{
		$start_tag = '{IF%'.$this->name.'}';
		$end_tag = '{'.$this->name.'%IF}';
		
		while( str_contains($text, $start_tag) ) {
			
			$start = strrpos($text, $start_tag);
			$end = strrpos($text, $end_tag, $start);
			if($end===false) {
				return;
			}
			
			$end += strlen($start_tag);
			
			$whole_block = substr( $text,  $start, $end-$start);
			$content = substr( $text, $start+strlen($start_tag), $end-$start-strlen($start_tag)-strlen($end_tag) );
			
			
			$evaluator = $this->getConditionEvaluator();
			
			if($block_item) {
				$pass = $evaluator( $block_item );
			} else {
				$pass = $evaluator();
			}
			
			if($pass) {
				$text = str_replace($whole_block, $content, $text);
			} else {
				$text = str_replace($whole_block, '', $text);
			}
		}
	}
	
	public function getInstructionTemplate() : string
	{
		return '{IF%'.$this->name.'} ... {'.$this->name.'%IF}';
	}
	
	
}