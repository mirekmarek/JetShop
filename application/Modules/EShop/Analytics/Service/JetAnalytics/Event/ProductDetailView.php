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
use JetApplication\Product_EShopData;

#[DataModel_Definition(
	name: 'ja_event_product_detail_view',
	database_table_name: 'ja_event_product_detail_view',
)]
class Event_ProductDetailView extends Event
{
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected string $product_id = '';
	
	public function cancelDefaultEvent(): bool
	{
		return true;
	}
	
	public function init( Product_EShopData $product ) : void
	{
		$this->product_id = $product->getId();
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Product detail view');
	}
	
	public function getCssClass(): string
	{
		return 'info';
	}
	
	
	public function showShortDetails(): string
	{
		return Application_Service_Admin::Product()->renderItemName( $this->product_id );
	}
	
	public function getIcon(): string
	{
		return 'face-grin-wide';
	}
	
	public function showLongDetails(): string
	{
		return '';
	}
}