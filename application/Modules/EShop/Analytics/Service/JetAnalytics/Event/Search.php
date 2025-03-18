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
	name: 'ja_event_search',
	database_table_name: 'ja_event_search',
)]
class Event_Search extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $search_query = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $found_something = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected array $result_ids = [];
	
	public function cancelDefaultEvent(): bool
	{
		return false;
	}

	public function init( string $q, array $result_ids ) : void
	{
		$this->search_query = $q;
		$this->found_something = count($result_ids);
		$this->result_ids = $result_ids;
	}
}