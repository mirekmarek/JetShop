<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Closure;
use Jet\Data_DateTime;
use Jet\Logger;
use JetApplication\Exports;
use JetApplication\Exports_Definition;
use JetApplication\Exports_Module;
use JetApplication\EShop;
use JetApplication\Exports_PlannedOutage;

abstract class Core_Exports_Definition {
	
	protected Exports_Module $module;
	
	protected string $code = '';
	protected string $name = '';
	protected string $description = '';
	
	protected string $export_code = '';
	protected ?Closure $export = null;
	
	
	public function __construct( Exports_Module $module, string $name, string $description, string $export_code, Closure $export )
	{
		$this->module = $module;
		$this->code = $module->getCode().':'.$export_code;
		
		$this->name = $name;
		$this->description = $description;
		
		$this->export_code = $export_code;
		$this->export = $export;
	}
	
	
	public function getModule(): Application_Module
	{
		return $this->module;
	}

	public function getCode(): string
	{
		return $this->code;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function getExportCode(): string
	{
		return $this->export_code;
	}
	
	
	public function isActive(): bool
	{
		foreach($this->getPlannedOutages() as $outage) {
			if($outage->isValid()) {
				return false;
			}
		}
		
		return true;
	}
	
	
	
	
	public function getURL( ?EShop $eshop = null ) : string
	{
		/**
		 * @var Exports_Definition $this
		 */
		return Exports::getManager()->getExportURL( $this, $eshop );
	}
	
	
	
	public function perform() : void
	{
		$this->export->call( $this->module );
	}
	
	/**
	 * @return Exports_PlannedOutage[]
	 */
	public function getPlannedOutages() : array
	{
		return Exports_PlannedOutage::getList( $this->code );
	}
	
	
	public function planOutage( ?Data_DateTime $from_date_time, ?Data_DateTime $till_date_time ) : void
	{
		if(!$from_date_time && !$till_date_time) {
			return;
		}
		
		Exports_PlannedOutage::new( $this->code, $from_date_time, $till_date_time );
		
		Logger::info(
			event: 'export:outage_scheduled',
			event_message: 'Export'.$this->code.' outage scheduled',
			context_object_id: $this->code,
			context_object_data: [
				'from_date_time' => $from_date_time,
				'till_date_time' => $till_date_time
			]
		);
		
	}
	
	public function cancelPlannedOutage( int $plan_id ) : void
	{
		foreach($this->getPlannedOutages() as $plan) {
			if($plan->getId()==$plan_id) {
				$plan->delete();
				
				Logger::info(
					event: 'export:scheduled_outage_cancelled',
					event_message: 'Export'.$this->code.' scheduled outage cancelled',
					context_object_id: $this->code,
					context_object_data: $plan
				);
				
				return;
			}
		}
	}
	
	public function shutdown() : void
	{
		foreach($this->getPlannedOutages() as $outage) {
			if(
				$outage->isValid() &&
				$outage->getFromDateTime() &&
				$outage->getTillDateTime()
			) {
				return;
			}
		}
		
		Exports_PlannedOutage::new( $this->code, null, null );
		
		Logger::info(
			event: 'export:shutdown',
			event_message: 'Export '.$this->code.' shutdown',
			context_object_id: $this->code
		);
	}
	
	
	public function start() : void
	{
		$started = false;
		foreach($this->getPlannedOutages() as $outage) {
			if(
				$outage->isValid()
			) {
				$outage->delete();
				$started = true;
			}
		}
		
		if($started) {
			Logger::info(
				event: 'export:start',
				event_message: 'Export '.$this->code.' start',
				context_object_id: $this->code
			);
		}
		
	}
	
}