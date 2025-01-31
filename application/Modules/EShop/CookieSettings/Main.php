<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\EShop\CookieSettings;


use Jet\Http_Request;
use Jet\Tr;
use JetApplication\EShop_CookieSettings_Manager;
use JetApplication\EShop_CookieSettings_Group;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;


class Main extends EShop_CookieSettings_Manager implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public const COOKIE_NAME = 'js_cookie_settings';
	
	/**
	 * @return EShop_CookieSettings_Group[]
	 */
	protected function initGroups() :array {
		$groups = [];
		
		$marketing = new EShop_CookieSettings_Group();
		$marketing->setCode( EShop_CookieSettings_Group::MARKETING );
		$marketing->setTitle( Tr::_('Marketing') );
		/** @noinspection SpellCheckingInspection */
		$marketing->setDescription( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integer rutrum, orci vestibulum ullamcorper ultricies, lacus quam ultricies odio, vitae placerat pede sem sit amet enim. In laoreet, magna id viverra tincidunt, sem odio bibendum justo, vel imperdiet sapien wisi sed libero.' );
		$marketing->setChecked( true );
		$groups[$marketing->getCode()] = $marketing;
		
		
		$mesurement = new EShop_CookieSettings_Group();
		$mesurement->setCode( EShop_CookieSettings_Group::MESUREMENT );
		$mesurement->setTitle( Tr::_('Mesurement') );
		/** @noinspection SpellCheckingInspection */
		$mesurement->setDescription( 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.' );
		$mesurement->setChecked( true );
		$groups[$mesurement->getCode()] = $mesurement;
		
		$stats = new EShop_CookieSettings_Group();
		$stats->setCode( EShop_CookieSettings_Group::STATS );
		$stats->setTitle( Tr::_('Stats') );
		/** @noinspection SpellCheckingInspection */
		$stats->setDescription( 'Sed ac dolor sit amet purus malesuada congue. Fusce tellus. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Nulla turpis magna, cursus sit amet, suscipit a, interdum id, felis.' );
		$stats->setChecked( true );
		$groups[$stats->getCode()] = $stats;
		
		return $groups;
	}
	
	
	public function groupEnabled( string $group_code ) : bool
	{
		if(empty($_COOKIE[static::COOKIE_NAME])) {
			return true;
		}
		
		return in_array($group_code, $this->getEnabledGroups());
	}
	
	/**
	 * @return EShop_CookieSettings_Group[]
	 */
	protected function getEnabledGroups() : array
	{
		$cookie_data = $this->readCookieData();
		if(!$cookie_data) {
			return [];
		}
		
		return $cookie_data->getEnabledGroups();
	}
	
	
	public function settingsRequired() : bool
	{
		$cookie_data = $this->readCookieData();
		if(
			!$cookie_data ||
			!$cookie_data->isValid()
		) {
			return true;
		}
		
		return false;
	}
	
	
	protected function setEnabledGroups( array $group_codes ) : void
	{
		$cookie_data = $this->readCookieData();
		if(!$cookie_data) {
			$cookie_data = new CookieData();
			$cookie_data->initNew();
		}
		
		$cookie_data->enableGroups( $group_codes );
		
		if($cookie_data->isAgree()) {
			$this->logAgree( $cookie_data->getEnabledGroups(), $cookie_data->isCompleteAgree() );
		} else {
			$this->logDisagree();
		}
		
		
		$this->writeCookiesData( $cookie_data );
	}
	
	public function resetSettings() : void
	{
		if(isset($_COOKIE[static::COOKIE_NAME])) {
			unset($_COOKIE[static::COOKIE_NAME]);
			setcookie(static::COOKIE_NAME, '', -1);
		}
	}
	
	protected function readCookieData( bool $create_default=false ) : ?CookieData
	{
		if(!empty($_COOKIE[static::COOKIE_NAME])) {
			$cookie_data = new CookieData( $_COOKIE[static::COOKIE_NAME] );
			
			return $cookie_data;
		}
		
		return null;
	}
	
	protected function writeCookiesData( CookieData $cookie_data) : void
	{
		
		$cookie_data = $cookie_data->toString();
		
		setcookie(
			static::COOKIE_NAME,
			$cookie_data,
			strtotime('+10 years')
		);
		
		$_COOKIE[static::COOKIE_NAME] = $cookie_data;
		
	}
	
	public function renderDialog() : string
	{
		if(!$this->settingsRequired()) {
			return '';
		}
		
		$GET = Http_Request::GET();
		if(($action=$GET->getString('cookie_settings'))) {
			switch($action) {
				case 'accept_all':
					$this->allowAll();
					break;
				case 'reject_all':
					$this->denyAll();
					break;
				case 'custom':
					$groups = explode(',', $GET->getString('groups'));
					$this->enableCustom( $groups );
					break;
			}
		}
		
		
		return $this->getView()->render('dialog');
	}
	
}