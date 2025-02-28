<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\Admin_UserSettings;

#[DataModel_Definition(
	name: 'admin_user_settings',
	database_table_name: 'admin_user_settings',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Admin_UserSettings extends DataModel {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true
	)]
	protected int $user_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected array $hiden_menus = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected array $hiden_items = [];
	
	
	protected static ?Admin_UserSettings $settings = null;
	
	public static function get() : Admin_UserSettings
	{
		if(!static::$settings) {
			$user_id = Auth::getCurrentUser()->getId();
			
			$settings = Admin_UserSettings::load( $user_id );
			if(!$settings) {
				$settings = new Admin_UserSettings();
				$settings->user_id = $user_id;
				$settings->save();
			}
			
			static::$settings = $settings;
		}
		
		return static::$settings;
	}
	
	public function getHidenMenus(): array
	{
		return $this->hiden_menus;
	}
	
	public function setHidenMenus( array $hiden_menus ): void
	{
		$this->hiden_menus = $hiden_menus;
	}
	
	public function getHidenItems(): array
	{
		return $this->hiden_items;
	}
	
	public function setHidenItems( array $hiden_items ): void
	{
		$this->hiden_items = $hiden_items;
	}
	
	
	
}