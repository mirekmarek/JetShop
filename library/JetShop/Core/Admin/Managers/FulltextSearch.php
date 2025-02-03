<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Fulltext Search',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_FulltextSearch extends Application_Module
{
	abstract public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void;
	abstract public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void;
	
	abstract public function  renderWhisperer(
		string  $name,
		string  $entity_type,
		string  $on_select,
		?string $object_type_filter=null,
		?bool   $object_is_active_filter=null
	);
	
	abstract public function  search(
		string  $entity_type,
		string  $search_string,
		?string $object_type_filter=null,
		?bool   $object_is_active_filter=null
	) : array;
	
}