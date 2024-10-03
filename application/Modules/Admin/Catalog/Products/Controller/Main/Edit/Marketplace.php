<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Http_Request;
use Jet\Tr;
use JetApplication\MarketplaceIntegration;
use JetApplication\Shops;


trait Controller_Main_Edit_Marketplace
{
	
	
	public function marketplace_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Marketplace') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		
		$selected_mp = null;
		$selected_mp_shop = null;
		
		$GET = Http_Request::GET();
		
		$mp = $GET->getString('mp');
		if($mp) {
			$selected_mp = MarketplaceIntegration::getActiveModule($mp);
			
			if($selected_mp) {
				$mp_shop = $GET->getString('mp_shop');
				if($mp_shop) {
					$selected_mp_shop = Shops::get($mp_shop);
					if($selected_mp_shop) {
						if(!$selected_mp->isAllowedForShop($selected_mp_shop)) {
							$selected_mp = null;
							$selected_mp_shop = null;
						}
					} else {
						$selected_mp = null;
					}
				}
			}
		}
		
		$this->view->setVar('selected_mp', $selected_mp );
		$this->view->setVar('selected_mp_shop', $selected_mp_shop );
		
		$this->view->setVar('selected_mp_code', $selected_mp?->getCode());
		$this->view->setVar('selected_mp_shop_key', $selected_mp_shop?->getKey() );
		
		$this->view->setVar('item', $product);
		$this->output( 'edit/marketplace' );
	}
	
	
}