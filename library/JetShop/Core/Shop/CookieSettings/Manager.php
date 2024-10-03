<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Data_DateTime;
use Jet\Http_Request;
use JetApplication\Shop_CookieSettings_Evidence_Agree;
use JetApplication\Shop_CookieSettings_Evidence_Disagree;
use JetApplication\Shop_CookieSettings_Group;
use JetApplication\Shops;

abstract class Core_Shop_CookieSettings_Manager extends Application_Module {
	

	protected ?array $groups = null;
	
	/**
	 * @return Shop_CookieSettings_Group[]
	 */
	abstract protected function initGroups() : array;
	
	/**
	 * @return Shop_CookieSettings_Group[]
	 */
	public function getGroups() : array
	{
		if($this->groups===null) {
			$this->groups = $this->initGroups();
		}
		
		return $this->groups;
	}
	
	public function groupAllowed( string $group_code ) : bool
	{
		$groups = $this->getGroups();
		if(!isset( $groups[$group_code])) {
			return false;
		}
		
		return $groups[$group_code]->getEnabled();
	}
	
	
	abstract public function groupEnabled( string $group_code ) : bool;
	
	
	public function enableGroup( string $group_code ) : void
	{
		$enabled = $this->getEnabledGroups();
		if(in_array($group_code, $enabled)) {
			return;
		}
		
		$enabled[] = $group_code;
		$this->setEnabledGroups($enabled);
	}
	
	public function disableGroup( string $group_code ) : void
	{
		$enabled = $this->getEnabledGroups();
		if(!in_array($group_code, $enabled)) {
			return;
		}
		
		$_enabled = [];
		
		foreach($enabled as $_g_id) {
			if($_g_id!=$group_code) {
				$_enabled[] = $_g_id;
			}
		}
		
		$this->setEnabledGroups($_enabled);
	}
	

	abstract public function resetSettings() : void;
	
	
	/**
	 * @return Shop_CookieSettings_Group[]
	 */
	abstract protected function getEnabledGroups() : array;
	
	abstract public function settingsRequired() : bool;
	
	
	public function denyAll() : void
	{
		$this->setEnabledGroups([]);
	}
	
	public function allowAll() : void
	{
		$this->setEnabledGroups(array_keys($this->getGroups()));
	}
	

	public function enableCustom( array $group_codes ) : void
	{
		$this->setEnabledGroups($group_codes);
	}
	
	protected function logAgree( array $enabled_groups, bool $complete_agree ) : void
	{
		$agree = new Shop_CookieSettings_Evidence_Agree();
		$agree->setShop( Shops::getCurrent() );
		$agree->setIP( Http_Request::clientIP() );
		$agree->setDateTime( Data_DateTime::now() );
		$agree->setGroups( $enabled_groups );
		$agree->setCompleteAgree( $complete_agree );
		$agree->save();
	}
	
	protected function logDisagree() : void
	{
		$disagree = new Shop_CookieSettings_Evidence_Disagree();
		$disagree->setShop( Shops::getCurrent() );
		$disagree->setIP( Http_Request::clientIP() );
		$disagree->setDateTime( Data_DateTime::now() );
		$disagree->save();
	}
	
	abstract public function renderDialog() : string;
}