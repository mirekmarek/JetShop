<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\AutoOffers;

use Jet\Application_Module;
use JetApplication\Marketing_AutoOffer;
use JetApplication\Shop_Managers;
use JetApplication\Shop_Managers_AutoOffers;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplication\Shops;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_AutoOffers, Shop_ModuleUsingTemplate_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	
	public function handleShoppingCart(): string
	{
		$view = $this->getView();
		
		$_auto_offers = Marketing_AutoOffer::getAllActive( Shops::getCurrent(), order_by: ['priority'] );
		
		$auto_offers = [];
		foreach($_auto_offers as $offer) {
			if( $offer->isRelevant( Shop_Managers::ShoppingCart()->getCart()->getProductIds() ) ) {
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