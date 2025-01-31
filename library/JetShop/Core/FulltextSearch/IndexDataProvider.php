<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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