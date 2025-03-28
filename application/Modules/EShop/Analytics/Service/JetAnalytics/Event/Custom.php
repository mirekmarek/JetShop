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

#[DataModel_Definition(
	name: 'ja_event_custom',
	database_table_name: 'ja_event_custom',
)]
class Event_Custom extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $event = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected array $event_data = [];
	
	public function cancelDefaultEvent(): bool
	{
		return false;
	}
	
	public function init( string $evetnt, array $event_data ) : void
	{
		$this->event = $evetnt;
		$this->event_data = $event_data;
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Custom event');
	}
	
	public function getCssClass(): string
	{
		return 'info';
	}
	
	
	public function showShortDetails(): string
	{
		//TODO:
		return '';
	}
	
	public function getIcon(): string
	{
		return 'gears';
	}
	
	public function showLongDetails(): string
	{
		//TODO:
		return '';
	}
}