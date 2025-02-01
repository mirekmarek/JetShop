<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProduct;



use Jet\Application;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\MarketplaceIntegration;
use JetApplication\EShops;
use JetApplication\Exports;
use JetApplication\KindOfProduct;

use Jet\Http_Request;
use Jet\Tr;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Kind of product';
	}
	
	
	public function getTabs(): array
	{
		$_tabs = parent::getTabs();
		$_tabs = array_merge(
			$_tabs,
			[
				'properties' => Tr::_( 'Properties' ),
				'exports'    => Tr::_( 'Exports' ),
				'marketplaces'    => Tr::_( 'Marketplaces' ),
			]
		);
		
		
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
		
		$this->router->addAction('edit_marketplaces', Main::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='marketplaces';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'marketplaces'], [ 'action'] );
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
			
			switch($GET->getString('p_action')) {
				
				case 'add_group':
					$group_id = $GET->getInt('group_id');
					
					$kind_of_product->addPropertyGroup( $group_id );
					
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
	
				case 'remove_group':
					$group_id = $GET->getInt('group_id');
					
					$kind_of_product->removePropertyGroup( $group_id );
					
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
					
				case 'set_layout':
					$kind_of_product->sortLayout( $GET->getString('layout') );
					
					
					Application::end();
					break;
				case 'add_property':
					$kind_of_product->addProperty( $GET->getInt('property_id'), $GET->getInt('group_id') );
					
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
				case 'remove_property':
					$kind_of_product->removeProperty( $GET->getInt('property_id') );
					
					echo $this->view->render( 'edit/properties/layout' );
					Application::end();
					break;
				case 'set_is_variant_master':
					$state = $GET->getBool('state');
					$property_id = $GET->getInt('property_id');
					$kind_of_product->setPropertyIsVariantMaster($property_id, $state );
					
					Application::end();
					break;
				
				case 'set_show_on_product_detail':
					$state = $GET->getBool('state');
					$property_id = $GET->getInt('property_id');
					$kind_of_product->setShowOnProductDetail($property_id, $state );
					
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
		$selected_exp_eshop = null;
		
		$exp = $GET->getString('exp');
		if($exp) {
			$selected_exp = Exports::getExportModule($exp);
			
			if($selected_exp) {
				$exp_eshop = $GET->getString('exp_eshop');
				if($exp_eshop) {
					$selected_exp_eshop = EShops::get($exp_eshop);
					if($selected_exp_eshop) {
						if(!$selected_exp->isAllowedForShop($selected_exp_eshop)) {
							$selected_exp = null;
							$selected_exp_eshop = null;
						}
					} else {
						$selected_exp = null;
					}
				}
			}
		}
		
		if($selected_exp_eshop) {
			$this->view->setVar('selected_exp', $selected_exp );
			$this->view->setVar('selected_exp_eshop', $selected_exp_eshop );
			
			$this->view->setVar('selected_exp_code', $selected_exp->getCode());
			$this->view->setVar('selected_exp_eshop_key', $selected_exp_eshop->getKey() );
			
			$this->view->setVar( 'kind_of_product', $kind_of_product );
		}
		
		
		
		$this->output( 'edit/exports' );
	}
	
	public function edit_marketplaces_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Marketplaces') );
		
		$kind_of_product = $this->current_item;
		
		$GET = Http_Request::GET();
		

		$selected_mp = null;
		$selected_mp_eshop = null;
		
		$mp = $GET->getString('mp');
		if($mp) {
			$selected_mp = MarketplaceIntegration::getActiveModule($mp);
			
			if($selected_mp) {
				$mp_eshop = $GET->getString('mp_eshop');
				if($mp_eshop) {
					$selected_mp_eshop = EShops::get($mp_eshop);
					if($selected_mp_eshop) {
						if(!$selected_mp->isAllowedForShop($selected_mp_eshop)) {
							$selected_mp = null;
							$selected_mp_eshop = null;
						}
					} else {
						$selected_mp = null;
					}
				}
			}
		}
		
		if($selected_mp_eshop) {
			$this->view->setVar('selected_mp', $selected_mp );
			$this->view->setVar('selected_mp_eshop', $selected_mp_eshop );
			
			$this->view->setVar('selected_mp_code', $selected_mp->getCode());
			$this->view->setVar('selected_mp_eshop_key', $selected_mp_eshop->getKey() );
			
			$this->view->setVar( 'kind_of_product', $kind_of_product );
		}
		
		
		
		$this->output( 'edit/marketplace' );
		
	}
	
	
}
