<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\AutoOffers;


use Jet\Application_Module;
use JetApplication\Marketing_AutoOffer;
use JetApplication\EShop_Managers;
use JetApplication\EShop_Managers_AutoOffers;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShops;


class Main extends Application_Module implements EShop_Managers_AutoOffers, EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function handleShoppingCart(): string
	{
		$view = $this->getView();
		
		$_auto_offers = Marketing_AutoOffer::getAllActive( EShops::getCurrent(), order_by: ['priority'] );
		
		$auto_offers = [];
		foreach($_auto_offers as $offer) {
			if( $offer->isRelevant( EShop_Managers::ShoppingCart()->getCart()->getProductIds() ) ) {
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