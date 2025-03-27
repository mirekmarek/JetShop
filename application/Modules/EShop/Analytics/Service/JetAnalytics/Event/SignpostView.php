<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Signpost_EShopData;

#[DataModel_Definition(
	name: 'ja_event_signpost_view',
	database_table_name: 'ja_event_signpost_view',
)]
class Event_SignpostView extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected string $signpost_id = '';
	
	public function cancelDefaultEvent(): bool
	{
		return true;
	}
	
	public function init( Signpost_EShopData $signpost ) : void
	{
		$this->signpost_id = $signpost->getId();
	}
	
}