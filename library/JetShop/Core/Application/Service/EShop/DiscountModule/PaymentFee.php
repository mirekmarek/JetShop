<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Application_Service_EShop_DiscountModule;
use Jet\Application_Service_MetaInfo;
use JetApplication\Marketing_PaymentFeeDiscount;


#[Application_Service_MetaInfo(
	multiple_mode: false,
	name: 'Discount module - payment fee',
)]
abstract class Core_Application_Service_EShop_DiscountModule_PaymentFee extends Application_Service_EShop_DiscountModule
{
	/**
	 * @return Marketing_PaymentFeeDiscount[]
	 */
	abstract public function getMarketingDiscounts(): array;

}