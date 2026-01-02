<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;

#[DataModel_Definition(
	name: 'ja_event_purchase_item',
	database_table_name: 'ja_event_purchase_item',
	parent_model_class: Event_Purchase::class,

)]
class Event_Purchase_Item extends Event_CheckoutStarted_Item
{
	/**
	 * @var Event_Purchase_Item_Category[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Event_Purchase_Item_Category::class
	)]
	protected array $categories = [];
	
	public function getCategoryIds() : array
	{
		$ids = [];
		
		foreach($this->categories as $category) {
			$ids[] = $category->getCategoryId();
		}
		
		return $ids;
	}

}