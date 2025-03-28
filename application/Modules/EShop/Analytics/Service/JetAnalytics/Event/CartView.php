<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Locale;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Currencies;
use JetApplication\MeasureUnits;
use JetApplication\Pricelists;
use JetApplication\ShoppingCart;

#[DataModel_Definition(
	name: 'ja_event_cart_view',
	database_table_name: 'ja_event_cart_view',
)]
class Event_CartView extends Event
{
	
	/**
	 * @var Event_CartView_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Event_CartView_Item::class
	)]
	protected array $items = [];
	
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
		type: DataModel::TYPE_FLOAT
	)]
	protected float $amount = 0.0;
	
	
	public function cancelDefaultEvent(): bool
	{
		return true;
	}
	
	public function init( ShoppingCart $cart ) : void
	{
		foreach($cart->getItems() as $item) {
			$this->items[] = Event_CartView_Item::createNew( $this, $item );
		}
		
		$pricelist = Pricelists::getCurrent();
		
		$this->currency_code = $pricelist->getCurrencyCode();
		$this->pricelist_code = $pricelist->getCode();
		$this->amount = $cart->getAmount();
		
		if($this->items) {
			$this->session->setShoppingCartUsed( true );
		}
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Cart view');
	}
	
	public function getCssClass(): string
	{
		return 'info';
	}
	
	public function getIcon() : string
	{
		return 'cart-shopping';
	}
	
	public function showShortDetails(): string
	{
		$res = '';
		
		$pricelist = Pricelists::get( $this->pricelist_code );
		$price_formatter = Admin_Managers::PriceFormatter();
		
		$amount =  $price_formatter->formatWithCurrency( $pricelist, $this->amount );
		$units = 0;
		foreach($this->items as $item) {
			$units += $item->getNumberOfUnits();
		}
		
		return
			Tr::_('Units: <b>%UNITS%</b>', ['UNITS' => $units])
			.'<br>'
			.Tr::_('Amount: <b>%AMOUNT%</b>', ['AMOUNT' => $amount]);
	}
	
	public function showLongDetails(): string
	{
		$res = '';
		
		$price_formatter = Admin_Managers::PriceFormatter();
		
		$res = '<table class="table table-striped">';
		$res .= '<thead><tr>';
		$res .= '<th>'.Tr::_('Number of units').'</th>';
		$res .= '<th>'.Tr::_('Product').'</th>';
		$res .= '<th>'.Tr::_('Price per unit').'</th>';
		$res .= '<th>'.Tr::_('Total').'</th>';
		$res .= '</tr></thead>';
		
		foreach($this->items as $item) {
			$unit = MeasureUnits::get( $item->getMeasureUnitCode() );
			$currency = Currencies::get( $item->getCurrencyCode() );
			$pricelist = Pricelists::get( $item->getPricelistCode() );
			
			$res .= '<tr>';
			
			if(!$unit) {
				$res .= '<td>'.$item->getNumberOfUnits().'</td>';
			} else {
				$res .= '<td>'.$unit->round($item->getNumberOfUnits()).' '.$unit->getNAme(Locale::getCurrentLocale()).'</td>';
			}
			$res .= '<td>'.Admin_Managers::Product()->renderItemName( $item->getProductId() ).'</td>';
			
			$res .= '<td>'.$price_formatter->formatWithCurrency( $pricelist, $item->getPricePerUnit() ).'</td>';
			$res .= '<td>'.$price_formatter->formatWithCurrency( $pricelist, $item->getPricePerUnit()*$item->getNumberOfUnits() ).'</td>';
			$res .= '</tr>';
		}
		
		$res .= '</table>';
		
		return $res;
	}
	
	
}