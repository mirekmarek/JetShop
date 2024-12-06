<?php
namespace JetShop;

use JetApplication\EShop;

interface Core_FulltextSearch_IndexDataProvider {
	
	public static function getEntityType() : string;
	public function getId() : int;
	
	public function getFulltextObjectType() : string;
	public function getFulltextObjectIsActive() : bool;
	
	public function getInternalFulltextObjectTitle() : string;
	public function getInternalFulltextTexts() : array;
	
	public function getShopFulltextTexts( EShop $eshop ) : array;
	
	public function updateFulltextSearchIndex() : void;
	public function removeFulltextSearchIndex() : void;
	
}