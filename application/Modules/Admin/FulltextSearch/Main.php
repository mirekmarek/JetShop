<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\FulltextSearch;


use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\MVC;
use Jet\Translator;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Admin_Managers_FulltextSearch;
use JetApplication\Application_Admin;


class Main extends Application_Module implements Admin_Managers_FulltextSearch
{
	public const WHISPERER_PAGE_ID = 'search-whisperer';
	protected static bool $whisperer_main_included = false;
	
	
	public function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		Index::deleteIndex( $object );
	}
	
	public function updateIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		Index::updateIndex( $object );
	}
	
	public function renderWhisperer(
		string  $name,
		string  $entity_type,
		string  $on_select,
		string|array|null $object_type_filter=null,
		?bool $object_is_active_filter=null
	) : string
	{
		$res = '';
		
		Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use (&$res, $entity_type, $object_type_filter, $object_is_active_filter, $name, $on_select) {
				$page = MVC::getPage(
					page_id: static::WHISPERER_PAGE_ID,
					base_id: Application_Admin::getBaseId()
				);
				
				$GET_params = [
					'class' => $entity_type
				];
				
				if($object_type_filter!==null) {
					$GET_params['type'] = is_array($object_type_filter) ? implode(',', $object_type_filter) : $object_type_filter;
				}
				
				if($object_is_active_filter!==null) {
					$GET_params['active'] = $object_is_active_filter;
				}
				
				$w_URL = $page->getURLPath(GET_params: $GET_params);
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar('name', $name);
				$view->setVar('on_select', $on_select);
				$view->setVar('w_URL', $w_URL);
				
				$res = '';
				if(!static::$whisperer_main_included) {
					$res .= $view->render('whisperer/main');
					static::$whisperer_main_included = true;
				}
				
				$res .= $view->render('whisperer/whisperer');
				
			}
		);
		
		return $res;
	}
	
	public function  search(
		string  $entity_type,
		string  $search_string,
		?string $object_type_filter=null,
		?bool   $object_is_active_filter=null
	) : array {
		$result = Index::search(
			entity_type: $entity_type,
			search_string: $search_string,
			object_type_filter:  $object_type_filter,
			object_is_active_filter: $object_is_active_filter
		);
		
		if(!$result) {
			return [];
		}
		
		$ids = [];
		
		foreach( $result as $r ) {
			$ids[] = $r->getObjectId();
		}
		
		return $ids;
	}
	
	
}