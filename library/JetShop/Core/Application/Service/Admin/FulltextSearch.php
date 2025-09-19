<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_Admin;
use JetApplication\FulltextSearch_IndexDataProvider;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Fulltext Search',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_FulltextSearch extends Application_Module
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