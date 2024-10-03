<?php
namespace JetShop;

use JetApplication\Shops_Shop;

interface Core_FulltextSearch_IndexDataProvider {
	
	public static function getEntityType() : string;
	public function getId() : int;
	
	public function getFulltextObjectType() : string;
	public function getFulltextObjectIsActive() : bool;
	
	public function getInternalFulltextObjectTitle() : string;
	public function getInternalFulltextTexts() : array;
	
	public function getShopFulltextTexts( Shops_Shop $shop ) : array;
	
	public function updateFulltextSearchIndex() : void;
	public function removeFulltextSearchIndex() : void;
	
}