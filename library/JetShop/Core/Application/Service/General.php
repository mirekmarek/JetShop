<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_General_Calendar;
use JetApplication\Application_Service_General_DeliveryTerm;
use JetApplication\Application_Service_General_DiscountsManager;
use JetApplication\EShopConfig;
use JetApplication\Application_Service_General_Invoices;
use JetApplication\Application_Service_General_EMailMarketingSubscribeManager;
use JetApplication\Application_Service_General_ExportsManager;
use JetApplication\Application_Service_General_Files;
use JetApplication\Application_Service_List;
use JetApplication\Application_Service_General_NumberSeries;
use JetApplication\Application_Service_General_PDFGenerator;
use JetApplication\Application_Service_General_SysServices;
use JetApplication\Application_Service_General_Translator;
use JetApplication\Application_Service_General_WarehouseManagement;
use JetApplication\Application_Service_General_ConversionSourceDetector;


class Core_Application_Service_General {
	
	public const GROUP = 'general';
	
	protected static ?Application_Service_List $list = null;
	
	public static function list(): Application_Service_List
	{
		if(!static::$list) {
			static::$list = new Application_Service_List(
				EShopConfig::getRootDir().'services/general.php',
				static::GROUP
			);
		}
		
		return static::$list;
	}
	
	
	public static function DiscountsManager() : Application_Service_General_DiscountsManager|Application_Module|null
	{
		return static::list()->get( Application_Service_General_DiscountsManager::class );
	}
	
	public static function EMailMarketingSubscribe() : Application_Service_General_EMailMarketingSubscribeManager|Application_Module|null
	{
		return static::list()->get( Application_Service_General_EMailMarketingSubscribeManager::class );
	}
	
	
	public static function Exports() : Application_Service_General_ExportsManager|Application_Module|null
	{
		return static::list()->get( Application_Service_General_ExportsManager::class );
	}
	
	public static function Files() : Application_Service_General_Files|Application_Module|null
	{
		return static::list()->get( Application_Service_General_Files::class );
	}
	
	public static function SysServices() : Application_Service_General_SysServices|Application_Module|null
	{
		return static::list()->get( Application_Service_General_SysServices::class );
	}
	
	public static function WarehouseManagement() : Application_Service_General_WarehouseManagement|Application_Module|null
	{
		return static::list()->get( Application_Service_General_WarehouseManagement::class );
	}
	
	public static function MarketingConversionSourceDetector() : Application_Service_General_ConversionSourceDetector|Application_Module|null
	{
		return static::list()->get( Application_Service_General_ConversionSourceDetector::class );
	}
	
	public static function NumberSeries() : Application_Service_General_NumberSeries|Application_Module|null
	{
		return static::list()->get( Application_Service_General_NumberSeries::class );
	}
	
	public static function Calendar() : Application_Service_General_Calendar|Application_Module|null
	{
		return static::list()->get( Application_Service_General_Calendar::class );
	}
	
	public static function DeliveryTerm() : Application_Service_General_DeliveryTerm|Application_Module|null
	{
		return static::list()->get( Application_Service_General_DeliveryTerm::class );
	}
	
	public static function Invoices() : Application_Service_General_Invoices|Application_Module|null
	{
		return static::list()->get( Application_Service_General_Invoices::class );
	}
	
	public static function Translator() : Application_Service_General_Translator|Application_Module|null
	{
		return static::list()->get( Application_Service_General_Translator::class );
	}
	
	public static function PDFGenerator() : Application_Service_General_PDFGenerator|Application_Module|null
	{
		return static::list()->get( Application_Service_General_PDFGenerator::class );
	}
	
	
}