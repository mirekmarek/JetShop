<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */


namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel_Definition;
use Jet\DataModel;
use Jet\Http_Request;
use Jet\Locale;
use Jet\MVC;
use Jet\Tr;

#[DataModel_Definition(
	name: 'ja_event_page_view',
	database_table_name: 'ja_event_page_view',
)]
class Event_PageView extends Event {

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $base_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE
	)]
	protected ?Locale $locale = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $page_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $URL = '';
	
	public function init() : void
	{
		$this->base_id = MVC::getBase()->getId()??'';
		$this->locale = MVC::getLocale();
		$this->page_id = MVC::getPage()->getId()??'';
		
		$this->URL = Http_Request::currentURL();
	}
	
	public function getBaseId() : string
	{
		return $this->base_id;
	}

	public function getLocale() : Locale
	{
		return $this->locale;
	}

	public function setPageId( string $value ) : void
	{
		$this->page_id = $value;
	}

	
	public function getUrl() : string
	{
		return $this->URL;
	}
	
	public function cancelDefaultEvent(): bool
	{
		return false;
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Common page view');
	}
	
	public function getCssClass(): string
	{
		return 'light';
	}
	
	
	public function showShortDetails(): string
	{
		return '<a href="'.$this->URL.'" target="_blank">'.$this->URL.'</a>';
	}
	
	public function getIcon(): string
	{
		return 'eye';
	}
	
	public function showLongDetails(): string
	{
		return '';
	}
}