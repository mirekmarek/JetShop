<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShop;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Fulltext Search',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_FulltextSearch extends Application_Module
{
	abstract public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void;
	abstract public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void;
	
	abstract public function  search(
		EShop  $eshop,
		string $entity_type,
		string $search_string
	) : array;
	
	abstract public function renderTopSearch(): string;
	
}