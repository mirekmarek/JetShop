<?php
namespace JetShop;

use JetApplication\ProductFilter;

interface Core_EShopEntity_HasProductsRelation_Interface {
	
	public static function getRelevanceModeScope() : array;
	public function getRelevanceMode(): string;
	public function setRelevanceMode( string $relevance_mode ): void;
	public function getProductsFilter() : ProductFilter;
	public function getProductIds() : array|bool;
	public function addProduct( int $product_id ) : bool;
	public function removeProduct( int $product_id ) : bool;
	public function removeAllProducts() : bool;
	public function isRelevant( array $product_ids ) : bool;

}