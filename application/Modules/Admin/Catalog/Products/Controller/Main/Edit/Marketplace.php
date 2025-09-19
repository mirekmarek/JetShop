<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Http_Request;
use Jet\Tr;
use JetApplication\MarketplaceIntegration;
use JetApplication\EShops;
use JetApplication\Product;


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
		$selected_mp_eshop = null;
		
		$GET = Http_Request::GET();
		
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
						} else {
							$selected_mp->init( $selected_mp_eshop );
						}
					} else {
						$selected_mp = null;
					}
				}
			}
		}
		
		$this->view->setVar('selected_mp', $selected_mp );
		$this->view->setVar('selected_mp_eshop', $selected_mp_eshop );
		
		$this->view->setVar('selected_mp_code', $selected_mp?->getCode());
		$this->view->setVar('selected_mp_eshop_key', $selected_mp_eshop?->getKey() );
		
		$this->view->setVar('item', $product);
		$this->output( 'edit/marketplace' );
	}
	
	
}