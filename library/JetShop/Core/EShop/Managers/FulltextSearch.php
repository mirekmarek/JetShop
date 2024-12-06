<?php
namespace JetShop;

use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShop;

interface Core_EShop_Managers_FulltextSearch
{
	public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void;
	public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void;
	
	public function  search(
		EShop  $eshop,
		string $entity_type,
		string $search_string
	) : array;
	
	public function renderTopSearch(): string;
	
}