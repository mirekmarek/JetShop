<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\UI;

use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\MVC_View;
use Jet\Navigation_Breadcrumb;
use Jet\MVC;
use Jet\Translator;
use Jet\UI;

use JetApplication\Admin_Managers_UI;
use JetApplication\Entity_WithCodeAndShopData;
use JetApplication\Entity_WithIDAndShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;


/**
 *
 */
class Main extends Application_Module implements Admin_Managers_UI
{

	protected MVC_View $view;
	
	public function initBreadcrumb() : void
	{
		$page = MVC::getPage();

		Navigation_Breadcrumb::reset();

		Navigation_Breadcrumb::addURL(
			UI::icon( $page->getIcon() ).'&nbsp;&nbsp;'.$page->getBreadcrumbTitle(),
			$page->getURL()
		);

	}
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	
	protected function render( $script ) : string
	{
		$res = '';
		
		Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use (&$res, $script) {
				$res = $this->view->render($script);
			}
		);
		
		return $res;
		
	}
	
	
	public function renderShopDataBlocks( ?Form $form=null, ?array $shops=null, bool $inline_mode=false, ?callable $renderer=null ) : void
	{
		if($shops===null) {
			$shops = Shops::getListSorted();
		}
		
		if(count($shops)<2) {
			foreach($shops as $shop) {
				$renderer( $shop, $shop->getKey() );
			}
			
			return;
		}
		
		if($inline_mode) {
			$this->view->setVar('form', $form);
			$this->view->setVar('shops', $shops);
			
			foreach($shops as $shop) {
				$this->view->setVar('shop', $shop );
				
				echo $this->render('shop-data-block/inline/block-start');
				$renderer( $shop, $shop->getKey() );
				echo $this->render('shop-data-block/inline/block-end');
			}
			
		} else {
			
			$tabs = [];
			foreach( $shops as $tab_shop ) {
				$tabs[$tab_shop->getKey()] = UI::flag( $tab_shop->getLocale() ).' '.$tab_shop->getShopName();
			}
			
			$shop_block_tabs = UI::tabsJS('set_shop', $tabs);
			
			$this->view->setVar('tabs', $shop_block_tabs);
			$this->view->setVar('form', $form);
			$this->view->setVar('shops', $shops);
			
			echo $this->render('shop-data-block/tabs/tabs-start');
			foreach($shops as $shop) {
				$this->view->setVar('shop', $shop );
				$this->view->setVar('tab', $shop_block_tabs->tab($shop->getKey()) );
				
				echo $this->render('shop-data-block/tabs/block-start');
				$renderer( $shop, $shop->getKey() );
				echo $this->render('shop-data-block/tabs/block-end');
			}
			echo $this->render('shop-data-block/tabs/tabs-end');
		}
		
	}
	
	
	public function renderSelectEntityWidget(
		string $name,
		string $caption,
		string $on_select,
		string $object_class,
		string|array|null $object_type_filter,
		?bool $object_is_active_filter,
		?string $selected_entity_title,
		?string $selected_entity_edit_URL
	) : string
	{
		$this->view->setVar('name', $name);
		$this->view->setVar('caption', $caption);
		$this->view->setVar('on_select', $on_select);
		$this->view->setVar('object_class', $object_class);
		$this->view->setVar('object_type_filter', $object_type_filter);
		$this->view->setVar('object_is_active_filter', $object_is_active_filter);
		$this->view->setVar('selected_entity_title', $selected_entity_title);
		$this->view->setVar('selected_entity_edit_URL', $selected_entity_edit_URL);
		
		return $this->render('select-entity-widget');
	}
	
	public function renderEntityActivation(
		Entity_WithIDAndShopData|Entity_WithCodeAndShopData $entity,
		bool $editable
	) : string
	{
		$this->view->setVar('entity', $entity);
		
		return $this->render(
			$editable ?
			'entity-activation/editable'
			:
			'entity-activation/readonly'
		);
	}
	
	public function renderEntityShopDataActivation(
		Entity_WithIDAndShopData|Entity_WithCodeAndShopData $entity,
		Shops_Shop $shop,
		bool $editable
	) : string
	{
		$this->view->setVar('entity', $entity);
		$this->view->setVar('shop_data', $entity->getShopData($shop));
		$this->view->setVar('shop', $shop);
		
		return $this->render(
			$editable ?
				'entity-shop-data-activation/editable'
				:
				'entity-shop-data-activation/readonly'
		);
	}
	
}