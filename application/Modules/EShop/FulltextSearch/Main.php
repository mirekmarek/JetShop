<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\FulltextSearch;

use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShop_Managers_FulltextSearch;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShop;


class Main extends EShop_Managers_FulltextSearch implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		Index::deleteIndex( $object );
	}
	
	public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		Index::updateIndex( $object );
	}
	
	public function  search(
		EShop  $eshop,
		string $entity_type,
		string $search_string
	) : array {
		return Index::search(
			eshop: $eshop,
			entity_type: $entity_type,
			search_string: $search_string
		);
	}
	
	
	public function renderTopSearch(): string
	{
		return $this->getView()->render('top_search');
	}
}