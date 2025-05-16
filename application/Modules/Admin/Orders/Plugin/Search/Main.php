<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


class Plugin_Search_Main extends Plugin
{
	public const KEY = 'search';
	

	public function hasDialog(): bool
	{
		return false;
	}
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return true;
	}
	
	
	public function handle(): void
	{
	}
	
	
	public function canBeHandled() : bool
	{
		return true;
	}
}