<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel_Definition;
use Jet\Tr;

#[DataModel_Definition(
	name: 'ja_event_search_whisperer',
	database_table_name: 'ja_event_search_whisperer',
)]
class Event_SearchWhisperer extends Event_Search
{
	
	public function getTitle(): string
	{
		return Tr::_('Search - whisperer');
	}
	
	public function getCssClass(): string
	{
		return 'light';
	}
	
	
	public function showShortDetails(): string
	{
		//TODO:
		return '';
	}
	
}