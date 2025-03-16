<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Category_EShopData;

#[DataModel_Definition(
	database_table_name: 'ja_event_category_view',
)]
class Event_CategoryView extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected string $category_id = '';
	
	public function cancelDefaultEvent(): bool
	{
		return true;
	}
	
	public function init( Category_EShopData $category ) : void
	{
		$this->category_id = $category->getId();
	}
	
}