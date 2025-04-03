<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Closure;
use JetApplication\Template_Condition;
use JetApplication\Template_Property;

abstract class Core_Template_Block {
	
	protected string $name = '';
	
	protected string $description = '';
	
	/**
	 * @var Core_Template_Property[]
	 */
	protected array $properties = [];
	
	/**
	 * @var Template_Condition[]
	 */
	protected array $conditions = [];
	
	protected Closure $item_list_creator;
	
	
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
	

	public function getItemListCreator(): Closure
	{
		return $this->item_list_creator;
	}
	
	public function setItemListCreator( Closure $item_list_creator ): void
	{
		$this->item_list_creator = $item_list_creator;
	}
	
	
	public function addProperty( string $name, string $description ) : Template_Property
	{
		$property = new Template_Property();
		$property->setName( $name );
		$property->setDescription( $description );
		$this->properties[$property->getName()] = $property;
		
		return $property;
	}
	
	public function addCondition( string $name, string $description ) : Template_Condition
	{
		$condition = new Template_Condition();
		$condition->setName( $name );
		$condition->setDescription( $description );
		$this->conditions[$condition->getName()] = $condition;
		
		return $condition;
	}
	
	/**
	 * @return Template_Condition[]
	 */
	public function getConditions(): array
	{
		return $this->conditions;
	}
	
	
	
	
	/**
	 * @return Template_Property[]
	 */
	public function getProperties(): array
	{
		return $this->properties;
	}
	
	public function process( string &$text  ) : void
	{
		$list_creator = $this->getItemListCreator();
		
		$items = $list_creator();
		
		/** @noinspection RegExpRedundantEscape */
		$reg_exp = '/\[%'.$this->name.'\]([^[]*)\['.$this->name.'%]/';
		
		if(!preg_match_all( $reg_exp, $text, $matches, PREG_SET_ORDER )) {
			return;
		}
		
		foreach($matches as $block) {
			$orig_str = $block[0];
			$block_template = $block[1];
			$block_txt = '';
			
			foreach($items as $item) {
				$new_block_txt = $block_template;
				
				
				foreach($this->getConditions() as $condition) {
					$condition->process( $new_block_txt, $item );
				}
				
				foreach( $this->getProperties() as $property ) {
					$property->process( $new_block_txt, $item );
				}
				
				$block_txt .= $new_block_txt;
			}
			
			$text = str_replace($orig_str, $block_txt, $text);
		}
	}
	
	public function getInstructionTemplate() : string
	{
		return '[%'.$this->name.'] ... ['.$this->name.'%]';
	}
	
	
}