<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Locale;
use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Application_Service_Admin;
use JetApplication\Discounts_Code;
use JetApplication\Discounts_Discount;

class Listing_Column_DiscountType extends Admin_Listing_Column
{
	public const KEY = 'discount_type';
	
	public function getTitle(): string
	{
		return Tr::_( 'Discount type' );
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Discounts_Code $item
		 */
		switch($item->getDiscountType()) {
			case Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_PERCENTAGE:
			case Discounts_Discount::DISCOUNT_TYPE_DELIVERY_PERCENTAGE:
				echo Locale::float( $item->getDiscount() ).'%';
				break;
			case Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_AMOUNT:
			case Discounts_Discount::DISCOUNT_TYPE_DELIVERY_AMOUNT:
				echo Application_Service_Admin::PriceFormatter()->formatWithCurrency( $item->getEshop()->getDefaultPricelist(), $item->getDiscount() );
				break;
		}

		return '';
	}
	
}