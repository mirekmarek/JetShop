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
	protected ProductListing $listing;
	
	protected bool $is_selected = false;
	
	public function __construct( ProductListing $listing )
	{
		$this->listing = $listing;
	}
	
	
	abstract public function getKey() : string;
	
	abstract public function getLabel() : string;
	
	abstract public function sort( array $product_ids ) : array;
	
	public function getIsSelected(): bool
	{
		return $this->is_selected;
	}
	
	public function setIsSelected( bool $is_selected ): void
	{
		$this->is_selected = $is_selected;
	}
	
	
}