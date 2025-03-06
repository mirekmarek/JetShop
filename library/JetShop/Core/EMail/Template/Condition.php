<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Closure;

abstract class Core_EMail_Template_Condition {
	
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
	
	
	
	public function processText( string &$text  ) : void
	{
		$reg_exp = '/{IF%'.$this->name.'}([^{}]*){'.$this->name.'%IF}/';
		
		if(!preg_match_all( $reg_exp, $text, $matches, PREG_SET_ORDER )) {
			return;
		}
		
		$evaluator = $this->getConditionEvaluator();
		
		$pass = $evaluator();
		
		if($pass) {
			foreach($matches as $block) {
				$orig_str = $block[0];
				$block_str = $block[1];
				$text = str_replace($orig_str, $block_str, $text);
			}
		} else {
			foreach($matches as $block) {
				$orig_str = $block[0];
				
				$text = str_replace($orig_str, '', $text);
			}
		}
	}
	
	public function getInstructionTemplate() : string
	{
		return '{IF%'.$this->name.'} ... {'.$this->name.'%IF}';
	}
	
	
}