<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Order\Delivery\Pricing;

use JetApplication\CashDesk;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Pricing_Module;
use JetApplication\Delivery_Pricing_PriceInfo;
use JetApplication\Discounts;
use JetApplication\Order_Item;
use JetApplication\Price;

/**
 *
 */
class Main extends Delivery_Pricing_Module
{
	public function getPrice( CashDesk $cash_desk, Delivery_Method $delivery_method ): Delivery_Pricing_PriceInfo
	{
		$price = new Delivery_Pricing_PriceInfo( $delivery_method );
		$dm_sh = $delivery_method->getShopData( $cash_desk->getShop() );

		$module = $delivery_method->getModule();
		if($module) {
			$standard_price = $module->getDefaultPrice( $cash_desk, $delivery_method );
		} else {
			$standard_price = $dm_sh->getDefaultPrice();
		}

		$price->setStandardPrice( $standard_price );
		$price->setFinalPrice( $standard_price );
		$price->setVatRate( $dm_sh->getVatRate() );

		if(
			$standard_price>0 &&
			!$dm_sh->getDiscountIsNotAllowed()
		) {
			foreach(Discounts::getActiveModules() as $dm) {
				foreach( $dm->getDiscounts( $cash_desk ) as $discount ) {
					if(
					!in_array($discount->getSubType(), [
						Order_Item::DISCOUNT_TYPE_DELIVERY_PERCENT,
						Order_Item::DISCOUNT_TYPE_DELIVERY_AMOUNT
					])
					) {
						continue;
					}

					if($discount->getSubType()==Order_Item::DISCOUNT_TYPE_DELIVERY_PERCENT) {
						$p = Price::round( $standard_price * ((100-$discount->getItemAmount())/100), $cash_desk->getShop() );
					} else {
						$p = $standard_price - $discount->getItemAmount();
					}
					if($p<0) {
						$p = 0;
					}
					$price->setIsPromoPrice(true);
					$price->setFinalPrice( $p );
					$price->setPromotionCode( $discount->getCode() );
					$price->setPromotionDescription( $discount->getTitle() );

					break 2;
				}
			}
		}

		return $price;
	}
}