<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use JetApplication\ProductListing;

abstract class Core_ProductListing_Sorter
{
	protected static string $key;
	protected ProductListing $listing;
	protected string $label = '';
	
	protected bool $is_selected = false;
	
	public function __construct()
	{
	}
	
	public function setListing( ProductListing $listing ): void
	{
		$this->listing = $listing;
	}
	
	
	public static function getKey() : string
	{
		return static::$key;
	}
	
	public function getLabel(): string
	{
		return $this->label;
	}
	
	public function setLabel( string $label ): void
	{
		$this->label = $label;
	}
	
	public function getIsSelected(): bool
	{
		return $this->is_selected;
	}
	
	public function setIsSelected( bool $is_selected ): void
	{
		$this->is_selected = $is_selected;
	}
	
	abstract public function sort( array $product_ids ) : array;
	
}