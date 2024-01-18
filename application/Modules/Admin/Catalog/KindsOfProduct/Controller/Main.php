<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProduct;


use Jet\Application;
use JetApplication\Admin_Entity_WithShopData_Manager_Controller;
use JetApplication\Shops;
use JetApplication\Exports;

use Jet\Http_Request;
use Jet\Tr;
use Jet\Logger;

class Controller_Main extends Admin_Entity_WithShopData_Manager_Controller
{
	
	
	
	public function getTabs(): array
	{
		$_tabs = [
			'main'   => Tr::_( 'Main data' ),
			'images' => Tr::_( 'Images' ),
			'properties' => Tr::_( 'Properties' ),
			'exports'    => Tr::_( 'Exports' ),
		
		];
		
		
		return $_tabs;
	}
	
	

	
	protected function setupRouter( string $action, string $selected_tab ) : void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_properties_settings', Main::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='properties';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'properties'], [ 'action'] );
			});
		
		
		$this->router->addAction('edit_exports', Main::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='exports';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'exports'], [ 'action'] );
			});
		
	}
	
	
	
	
	public function edit_properties_settings_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Properties') );
		
		/**
		 * @var KindOfProduct $kind_of_product
		 */
		$kind_of_product = $this->current_item;
		
		$this->view->setVar('item', $this->current_item);
		$this->view->setVar('listing', $this->getListing());
		$this->view->setVar( 'kind_of_product', $kind_of_product );
		
		
		$GET = Http_Request::GET();
		
		if($this->current_item->isEditable()) {
			
			$log = function( string $action, string $message ) use ($kind_of_product) {
				Logger::success(
					event: 'kind_of_product_updated.'.$action,
					event_message: 'Kind of Product updated - '.$message,
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			};
			
			switch($GET->getString('p_action')) {
				
				case 'add_group':
					$group_id = $GET->getInt('group_id');
					
					if($kind_of_product->addPropertyGroup( $group_id )) {
						$log(
							'detail_group_added',
							'detail group ' . $group_id . ' added'
						);
					}
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
	
				case 'remove_group':
					$group_id = $GET->getInt('group_id');
					
					if($kind_of_product->removePropertyGroup( $group_id )) {
						$log(
							'detail_group_removed',
							'detail group '.$group_id.' removed',
						);
					}
					
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
					
				case 'set_layout':
					$kind_of_product->sortLayout( $GET->getString('layout') );
					$log(
						'layout_sorted',
						'layout sorted',
					);
					
					Application::end();
					break;
				case 'add_property':
					if($kind_of_product->addProperty( $GET->getInt('property_id'), $GET->getInt('group_id') )) {
						$log(
							'detail_property_added',
							'detail property ' . $GET->getInt('property_id') . ' added',
						);
					}
					
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
				case 'remove_property':
					if($kind_of_product->removeProperty( $GET->getInt('property_id') )) {
						$log(
							'detail_property_removed',
							'detail property '.$GET->getInt('property_id').' removed'
						);
						
					}
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
				case 'set_is_variant_master':
					$state = $GET->getBool('state');
					$property_id = $GET->getInt('property_id');
					if($kind_of_product->setPropertyIsVariantMaster($property_id, $state )) {
						$log(
							'kind_of_product_updated.variant_master_set',
							'Kind of Product updated - variant master stet '.$property_id.':'.($state?1:0)
						);
					}
					
					Application::end();
					break;
				
				case 'set_use_in_filters':
					$state = $GET->getBool('state');
					$property_id = $GET->getInt('property_id');
					if($kind_of_product->setUseInFilters($property_id, $state )) {
						$log(
							'kind_of_product_updated.set_use_in_filters',
							'Kind of Product updated - use im filters '.$property_id.':'.($state?1:0)
						);
					}
					
					Application::end();
					break;
				
				case 'set_show_on_product_detail':
					$state = $GET->getBool('state');
					$property_id = $GET->getInt('property_id');
					if($kind_of_product->setShowOnProductDetail($property_id, $state )) {
						$log(
							'kind_of_product_updated.set_show_on_product_detail',
							'Kind of Product updated - show on product detail ' . $property_id . ':' . ($state ? 1 : 0)
						);
					}
					
					Application::end();
					break;
					
			}
			
		}
		
		
		$this->output( 'edit/properties' );
	}
	
	
	public function edit_exports_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Exports') );
		
		$kind_of_product = $this->current_item;
		
		$GET = Http_Request::GET();
		
		$selected_exp = null;
		$selected_exp_shop = null;
		
		$exp = $GET->getString('exp');
		if($exp) {
			$selected_exp = Exports::getActiveModule($exp);
			
			if($selected_exp) {
				$exp_shop = $GET->getString('exp_shop');
				if($exp_shop) {
					$selected_exp_shop = Shops::get($exp_shop);
					if($selected_exp_shop) {
						if(!$selected_exp->isAllowedForShop($selected_exp_shop)) {
							$selected_exp = null;
							$selected_exp_shop = null;
						}
					} else {
						$selected_exp = null;
					}
				}
			}
		}
		
		if($selected_exp_shop) {
			$this->view->setVar('selected_exp', $selected_exp );
			$this->view->setVar('selected_exp_shop', $selected_exp_shop );
			
			$this->view->setVar('selected_exp_code', $selected_exp->getCode());
			$this->view->setVar('selected_exp_shop_key', $selected_exp_shop->getKey() );
			
			$this->view->setVar( 'kind_of_product', $kind_of_product );
		}
		
		
		
		$this->output( 'edit/exports' );
	}
	
	
	
}
