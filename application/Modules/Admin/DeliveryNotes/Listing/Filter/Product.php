<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\DeliveryNotes;

use JetApplication\Admin_Listing_Filter_Product;
use JetApplication\DeliveryNote_Item;

class Listing_Filter_Product extends Admin_Listing_Filter_Product
{
	public const KEY = 'product';
	protected string $label = 'Product';
	
	public function generateWhere(): void
	{
		if(!$this->product_id) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'delivery_note_item.type' => [DeliveryNote_Item::ITEM_TYPE_PRODUCT, DeliveryNote_Item::ITEM_TYPE_VIRTUAL_PRODUCT],
			'AND',
			'delivery_note_item.item_id'   => $this->product_id,
		]);
	}

}