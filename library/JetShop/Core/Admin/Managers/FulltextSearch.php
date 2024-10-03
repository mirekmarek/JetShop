<?php
namespace JetShop;

use JetApplication\FulltextSearch_IndexDataProvider;

interface Core_Admin_Managers_FulltextSearch
{
	public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void;
	public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void;

	public function  renderWhisperer(
		string  $name,
		string  $entity_type,
		string  $on_select,
		?string $object_type_filter=null,
		?bool   $object_is_active_filter=null
	);
	
	public function  search(
		string  $entity_type,
		string  $search_string,
		?string $object_type_filter=null,
		?bool   $object_is_active_filter=null
	) : array;
	
}