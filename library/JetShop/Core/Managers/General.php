<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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
use JetApplication\PDF_Generator;
use JetApplication\SysServices_Manager;
use JetApplication\Translator_Manager;
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
		static::_registerManagers( Manager_MetaInfo::GROUP_GENERAL );
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
	
	public static function Translator() : Translator_Manager|Application_Module|null
	{
		return static::get( Translator_Manager::class );
	}
	
	public static function PDFGenerator() : PDF_Generator|Application_Module|null
	{
		return static::get( PDF_Generator::class );
	}
	
}