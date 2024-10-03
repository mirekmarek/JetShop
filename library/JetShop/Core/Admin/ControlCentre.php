<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Data_Text;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Managers;

abstract class Core_Admin_ControlCentre
{
	public const GROUP_MAIN = 'main';
	public const GROUP_SYSTEM = 'system';
	public const GROUP_EXPORTS = 'exports';
	public const GROUP_ANALYTICS = 'analytics';
	public const GROUP_PAYMENT = 'payment';
	public const GROUP_DELIVERY = 'delivery';
	public const GROUP_MARKET_PLACE_INTEGRATION = 'market_place_integration';
	
	protected static ?array $module_list = null;
	
	/**
	 * @return Admin_ControlCentre_Module_Interface[]|Application_Module[]
	 */
	public static function getModuleList() : array
	{
		if( static::$module_list===null ) {
			static::$module_list = [];
			
			foreach( Managers::findManagers(Admin_ControlCentre_Module_Interface::class) as $module_name=> $module ) {
				static::$module_list[ $module_name ] = $module;
			}

			uasort( static::$module_list, function( Admin_ControlCentre_Module_Interface $a, Admin_ControlCentre_Module_Interface $b ) {
				return strcmp(
					Data_Text::removeAccents($a->getControlCentreTitleTranslated()),
					Data_Text::removeAccents($b->getControlCentreTitleTranslated())
				);
			} );
			
			uasort( static::$module_list, function( Admin_ControlCentre_Module_Interface $a, Admin_ControlCentre_Module_Interface $b ) {
				return $a->getControlCentrePriority() <=> $b->getControlCentrePriority();
			} );
			
		}
		
		
		return static::$module_list;
	}
}