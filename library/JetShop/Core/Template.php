<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Template_Block;
use JetApplication\Template_Condition;
use JetApplication\Template_Property;
use JetApplication\EShop;

abstract class Core_Template {
	
	protected string $internal_name = '';
	
	protected string $internal_notes = '';
	
	/**
	 * @var Template_Property[]
	 */
	protected ?array $properties = null;
	
	/**
	 * @var Template_Block[]
	 */
	protected ?array $blocks = null;
	
	/**
	 * @var Template_Condition[]
	 */
	protected ?array $conditions = null;
	
	
	public function __construct()
	{
	}
	
	protected function initialize() : void
	{
		if($this->properties!==null) {
			return;
		}
		
		$this->properties = [];
		$this->blocks = [];
		$this->conditions = [];
		
		$this->init();
		
	}
	
	abstract protected function init() : void;
	abstract public function initTest( EShop $eshop ) : void;
	
	public function getInternalName(): string
	{
		$this->initialize();
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	public function getInternalCode(): string
	{
		return get_class($this);
	}
	
	public function setInternalCode( string $internal_code ): void
	{
	}
	
	public function getInternalNotes(): string
	{
		$this->initialize();
		return $this->internal_notes;
	}
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	
	/**
	 * @return Template_Property[]
	 */
	public function getProperties(): array
	{
		$this->initialize();
		return $this->properties;
	}
	
	/**
	 * @return Template_Block[]
	 */
	public function getBlocks(): array
	{
		$this->initialize();
		return $this->blocks;
	}
	
	/**
	 * @return Template_Condition[]
	 */
	public function getConditions(): array
	{
		$this->initialize();
		return $this->conditions;
	}
	
	public function addCondition( string $name, string $description ) : Template_Condition
	{
		$this->initialize();
		$condition = new Template_Condition();
		$condition->setName( $name );
		$condition->setDescription( $description );
		$this->conditions[$condition->getName()] = $condition;
		
		return $condition;
	}
	
	
	public function addProperty( string $name, string $description ) : Template_Property
	{
		$this->initialize();
		$property = new Template_Property();
		$property->setName( $name );
		$property->setDescription( $description );
		$this->properties[$property->getName()] = $property;
		
		return $property;
	}
	
	public function addPropertyBlock( string $name, string $description ) : Template_Block
	{
		$this->initialize();
		$block = new Template_Block();
		$block->setName( $name );
		$block->setDescription( $description );
		$this->blocks[$block->getName()] = $block;
		
		return $block;
	}
	
	
	public function process( string $text ) : string
	{
		$this->initialize();
		
		$processed = $text;
		
		foreach($this->getConditions() as $condition) {
			$condition->process( $processed );
		}
		
		foreach($this->getProperties() as $property) {
			$property->process( $processed );
		}
		
		foreach($this->getBlocks() as $block ) {
			$block->process( $processed );
		}

		return $processed;
	}
	
}