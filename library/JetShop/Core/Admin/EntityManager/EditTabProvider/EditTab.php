<?php

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetShop;

use Closure;
use Jet\Application_Module;
use Jet\BaseObject;
use Jet\Tr;
use JetApplication\EShopEntity_Basic;

abstract class Core_Admin_EntityManager_EditTabProvider_EditTab extends BaseObject
{
	protected EShopEntity_Basic $item;
	protected Application_Module $module;
	protected string $tab_key = '';
	protected string $tab_title = '';
	protected string $tab_icon = '';
	protected Closure $handler;
	
	public function __construct( EShopEntity_Basic $item, Application_Module $module ) {
		$this->item = $item;
		$this->module = $module;
	}
	
	public function setTab( string $tab_key, string $tab_title, string $tab_icon='' ): void
	{
		$this->tab_key = $tab_key;
		$this->tab_title = Tr::_( $tab_title, dictionary: $this->module->getModuleManifest()->getName() );
		$this->tab_icon = $tab_icon;
	}
	
	public function getModule(): Application_Module
	{
		return $this->module;
	}
	
	public function getTabKey(): string
	{
		return $this->tab_key;
	}
	
	public function getTabTitle(): string
	{
		return $this->tab_title;
	}
	
	public function getTabIcon(): string
	{
		return $this->tab_icon;
	}
	
	
	public function getHandler(): Closure
	{
		return $this->handler;
	}
	
	public function setHandler( Closure $handler ): void
	{
		$this->handler = $handler;
	}
	
	public function handle() : string
	{
		$handler = $this->handler;
		
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module->getModuleManifest()->getName(),
			action: function() use ($handler) {
				return $handler( $this->item );
			}
		);
	}
	
}