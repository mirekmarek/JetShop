<?php
namespace JetApplicationModule\Admin\Catalog\Brands;

use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\MarketplaceIntegration;
use JetApplication\EShops;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Brand';
	}
	
	public function getTabs(): array
	{
		$tabs = parent::getTabs();
		$tabs['marketplaces'] = Tr::_( 'Marketplaces' );
		
		return $tabs;
	}
	
	protected function setupRouter( string $action, string $selected_tab ) : void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_marketplaces', Main::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='marketplaces';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'marketplaces'], [ 'action'] );
			});
	}
	
	public function edit_marketplaces_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Marketplaces') );
		
		$brand = $this->current_item;
		
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
			
			$this->view->setVar( 'brand', $brand );
		}
		
		
		
		$this->output( 'edit/marketplace' );
		
	}
	
}