<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\Pricelists;
use JetApplication\ShoppingCart_Item;

#[DataModel_Definition(
	name: 'ja_event_remove_from_cart',
	database_table_name: 'ja_event_remove_from_cart',
)]
class Event_RemoveFromCart extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected string $product_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price_per_unit = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $pricelist_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $measure_unit_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $selected_gift_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $auto_offer_id = 0;
	
	
	public function cancelDefaultEvent(): bool
	{
		return true;
	}
	
	public function init( ShoppingCart_Item $cart_item ) : void
	{
		$pricelist = Pricelists::getCurrent();
		
		$this->product_id = $cart_item->getProductId();
		$this->number_of_units = $cart_item->getNumberOfUnits();
		$this->price_per_unit = $cart_item->getProduct()->getPrice( $pricelist );
		$this->currency_code = $pricelist->getCurrencyCode();
		$this->pricelist_code = $pricelist->getCode();
		$this->measure_unit_code = $cart_item->getMeasureUnit()?$cart_item->getMeasureUnit()->getCode():'';
		$this->selected_gift_id = $cart_item->getSelectedGiftId();
		$this->auto_offer_id = $cart_item->getAutoOfferId();
		
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Product removed from the cart');
	}
	
	public function getCssClass(): string
	{
		return 'danger';
	}
	
	public function getIcon() : string
	{
		return 'trash';
	}
	
	
	public function showShortDetails(): string
	{
		return Application_Service_Admin::Product()->renderItemName( $this->product_id );
	}
	
	public function showLongDetails(): string
	{
		return '';
	}
}