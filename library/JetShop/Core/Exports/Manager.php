<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Shops_Shop;
use JetApplication\Exports_Definition;

abstract class Core_Exports_Manager extends Application_Module {
	
	abstract public function handleExports() : void;
	
	abstract public function getExportURL( Exports_Definition $export, ?Shops_Shop $shop=null ) : string;
	
	public function shutdownExport( Exports_Definition $export ) : void
	{
		$export->shutdown();
	}
	
	public function startExport( Exports_Definition $export ) : void
	{
		$export->start();
	}
	
	public function planExportOutage( Exports_Definition $export, ?Data_DateTime $from_date_time, ?Data_DateTime $till_date_time ) : void
	{
		$export->planOutage( $from_date_time, $till_date_time );
	}
	
	public function cancelPlannedOutage( Exports_Definition $export, int $plan_id ) : void
	{
		$export->cancelPlannedOutage( $plan_id );
	}
	
}