<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\BaseObject;
use JetShop\Product;

abstract class Listing_Operation extends BaseObject
{
	protected Listing $listing;
	
	public function __construct( Listing $listing )
	{
		$this->listing = $listing;
	}
	
	abstract public function getKey(): string;
	
	abstract public function getTitle(): string;
	
	abstract public function isPrepared(): bool;
	
	/**
	 * @param Product[] $products
	 */
	abstract public function perform( array $products ): void;

}