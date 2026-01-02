<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\AutoOffers;

use JetApplication\CashDesk;
use JetApplication\Marketing_AutoOffer;
use JetApplication\Application_Service_EShop_AutoOffers;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;


class Main extends Application_Service_EShop_AutoOffers implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function handleShoppingCart( CashDesk $cash_desk ): string
	{
		$view = $this->getView();
		
		$cart = $cash_desk->getCart();
		
		$view->setVar('cash_desk', $cash_desk);
		$view->setVar('cart', $cart);
		
		$_auto_offers = Marketing_AutoOffer::getAllActive( $cash_desk->getEshop(), order_by: ['priority'] );
		
		$product_ids = $cart->getProductIds();
		
		$auto_offers = [];
		foreach($_auto_offers as $offer) {
			if( $offer->isRelevant( $product_ids ) ) {
				$auto_offers[$offer->getId()] = $offer;
			}
		}
		
		if(!$auto_offers) {
			return '';
		}
		
		$res = $view->render('main');
		
		foreach($auto_offers as $offer) {
			$view->setVar('offer', $offer);
			$res .= $view->render( $offer->getShowMode() );
		}
		
		
		return $res;
	}
}