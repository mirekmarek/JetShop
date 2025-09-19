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
	
	
	public function getTitle(): string
	{
		return Tr::_('Signopost view');
	}
	
	public function getCssClass(): string
	{
		return 'light';
	}
	
	
	public function showShortDetails(): string
	{
		return Application_Service_Admin::Signpost()->renderItemName( $this->signpost_id );
	}
	
	public function getIcon(): string
	{
		return 'signs-post';
	}
	
	public function showLongDetails(): string
	{
		return '';
	}
}