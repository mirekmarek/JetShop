<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\EShop;
use JetApplication\SysServices_Definition;

abstract class Core_SysServices_Manager extends Application_Module {

	abstract public function handleSysServices() : void;
	
	abstract public function getSysServiceURL( SysServices_Definition $service, ?EShop $eshop=null ) : string;
	
	public function shutdownService( SysServices_Definition $service ) : void
	{
		$service->shutdown();
	}
	
	public function startService( SysServices_Definition $service ) : void
	{
		$service->start();
	}
	
	public function planServiceOutage( SysServices_Definition $service, ?Data_DateTime $from_date_time, ?Data_DateTime $till_date_time ) : void
	{
		$service->planOutage( $from_date_time, $till_date_time );
	}
	
	public function cancelPlannedOutage( SysServices_Definition $service, int $plan_id ) : void
	{
		$service->cancelPlannedOutage( $plan_id );
	}
	
}