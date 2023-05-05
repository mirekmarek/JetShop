<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\BaseObject;
use Jet\Http_Headers;
use JetApplication\Product;

abstract class Listing_Operation extends BaseObject
{
	protected Listing $listing;
	
	/**
	 * @var Product[]
	 */
	protected array $products;
	
	public function __construct( Listing $listing )
	{
		$this->listing = $listing;
	}
	
	public function init( array $products ) : void
	{
		$this->products = $products;
	}
	
	/**
	 * @return Product[]
	 */
	public function getProducts() : array
	{
		return $this->products;
	}
	
	abstract public function getKey(): string;
	
	abstract public function getTitle(): string;
	
	abstract public function getOperationGetParams() : array;
	
	abstract public function isPrepared(): bool;
	
	abstract public function perform(): void;
	
	public function returnRedirect() : void
	{
		
		$get_params = array_merge([
			'listing_operation',
			'p',
			'listing_operation_confirm'
		], $this->getOperationGetParams());
		
		Http_Headers::reload(unset_GET_params: $get_params );
		
	}

}