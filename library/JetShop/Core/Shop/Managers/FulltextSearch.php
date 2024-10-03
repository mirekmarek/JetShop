<?php
namespace JetShop;

use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Shops_Shop;

interface Core_Shop_Managers_FulltextSearch
{
	public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void;
	public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void;
	
	public function  search(
		Shops_Shop $shop,
		string  $entity_type,
		string  $search_string
	) : array;
	
	public function renderTopSearch(): string;
	
}