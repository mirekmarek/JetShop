<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Application_Service_General;
use JetApplication\EShop;
use JetApplication\Exports_Definition;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: false,
	name: 'Data exports',
	description: '',
	module_name_prefix: 'Exports.'
)]
abstract class Core_Application_Service_General_ExportsManager extends Application_Module {
	
	abstract public function handleExports() : void;
	
	abstract public function getExportURL( Exports_Definition $export, ?EShop $eshop=null ) : string;
	
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