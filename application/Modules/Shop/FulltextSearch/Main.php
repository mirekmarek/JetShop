<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\FulltextSearch;

use Jet\Application_Module;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Shop_Managers_FulltextSearch;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplication\Shops_Shop;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_FulltextSearch, Shop_ModuleUsingTemplate_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	
	public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		Index::deleteIndex( $object );
	}
	
	public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		Index::updateIndex( $object );
	}
	
	public function  search(
		Shops_Shop $shop,
		string  $entity_type,
		string  $search_string
	) : array {
		return Index::search(
			shop: $shop,
			entity_type: $entity_type,
			search_string: $search_string
		);
	}
	
	
	public function renderTopSearch(): string
	{
		return $this->getView()->render('top_search');
	}
}