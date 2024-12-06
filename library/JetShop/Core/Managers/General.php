<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Calendar_Manager;
use JetApplication\DeliveryTerm_Manager;
use JetApplication\Discounts_Manager;
use JetApplication\EShopConfig;
use JetApplication\Invoice_Manager;
use JetApplication\Manager_MetaInfo;
use JetApplication\Managers;
use JetApplication\EMailMarketing_Subscribe_Manager;
use JetApplication\Exports_Manager;
use JetApplication\Files_Manager;
use JetApplication\NumberSeries_Manager;
use JetApplication\SysServices_Manager;
use JetApplication\WarehouseManagement_Manager;
use JetApplication\Marketing_ConversionSourceDetector_Manager;


class Core_Managers_General extends Managers {
	/**
	 * @var Manager_MetaInfo[]|null
	 */
	protected static ?array $managers_meta_info = null;
	
	protected static ?array $config = null;
	
	protected static array $managers = [];
	
	public static function getCfgFilePath() : string
	{
		return EShopConfig::getRootDir().'managers/general.php';
	}
	
	protected static function registerManagers() : void
	{
		static::registerManager(
			interface_class_name: Discounts_Manager::class,
			is_mandatory: true,
			name: 'Discounts',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: EMailMarketing_Subscribe_Manager::class,
			is_mandatory: true,
			name: 'E-mail marketing subscribe',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: Exports_Manager::class,
			is_mandatory: false,
			name: 'Data exports',
			description: '',
			module_name_prefix: 'Exports.'
		);
		
		static::registerManager(
			interface_class_name: Files_Manager::class,
			is_mandatory: true,
			name: 'Files',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: SysServices_Manager::class,
			is_mandatory: true,
			name: 'System services',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: WarehouseManagement_Manager::class,
			is_mandatory: false,
			name: 'Warehouse management',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: Marketing_ConversionSourceDetector_Manager::class,
			is_mandatory: false,
			name: 'Conversion source detector',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: NumberSeries_Manager::class,
			is_mandatory: true,
			name: 'Number series',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: Calendar_Manager::class,
			is_mandatory: true,
			name: 'Calendar',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: DeliveryTerm_Manager::class,
			is_mandatory: true,
			name: 'Delivery term',
			description: '',
			module_name_prefix: ''
		);
		
		static::registerManager(
			interface_class_name: Invoice_Manager::class,
			is_mandatory: true,
			name: 'Invoices',
			description: '',
			module_name_prefix: ''
		);
		
		
	}
	
	public static function Discounts() : Discounts_Manager|Application_Module|null
	{
		return static::get( Discounts_Manager::class );
	}
	
	public static function EMailMarketingSubscribe() : EMailMarketing_Subscribe_Manager|Application_Module|null
	{
		return static::get( EMailMarketing_Subscribe_Manager::class );
	}
	
	public static function Exports() : Exports_Manager|Application_Module|null
	{
		return static::get( Exports_Manager::class );
	}
	
	public static function Files() : Files_Manager|Application_Module|null
	{
		return static::get( Files_Manager::class );
	}
	
	public static function SysServices() : SysServices_Manager|Application_Module|null
	{
		return static::get( SysServices_Manager::class );
	}
	
	public static function WarehouseManagement() : WarehouseManagement_Manager|Application_Module|null
	{
		return static::get( WarehouseManagement_Manager::class );
	}
	
	public static function MarketingConversionSourceDetector() : Marketing_ConversionSourceDetector_Manager|Application_Module|null
	{
		return static::get( Marketing_ConversionSourceDetector_Manager::class );
	}
	
	public static function NumberSeries() : NumberSeries_Manager|Application_Module|null
	{
		return static::get( NumberSeries_Manager::class );
	}
	
	public static function Calendar() : Calendar_Manager|Application_Module|null
	{
		return static::get( Calendar_Manager::class );
	}
	
	public static function DeliveryTerm() : DeliveryTerm_Manager|Application_Module|null
	{
		return static::get( DeliveryTerm_Manager::class );
	}
	
	public static function Invoices() : Invoice_Manager|Application_Module|null
	{
		return static::get( Invoice_Manager::class );
	}
	
}