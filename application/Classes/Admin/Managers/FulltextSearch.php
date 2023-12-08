<?php
namespace JetApplication;

interface Admin_Managers_FulltextSearch
{
	public function addIndex( Admin_FulltextSearch_IndexDataProvider $object ) : void;
	public function deleteIndex( Admin_FulltextSearch_IndexDataProvider $object ) : void;
	public function updateIndex( Admin_FulltextSearch_IndexDataProvider $object ) : void;

	public function  renderWhisperer(
		string  $name,
		string  $object_class,
		string  $on_select,
		?string $object_type_filter=null,
		?bool   $object_is_active_filter=null
	);
	
	public function  search(
		string  $object_class,
		string  $search_string,
		?string $object_type_filter=null,
		?bool   $object_is_active_filter=null
	) : array;
	
}