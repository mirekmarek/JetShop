<?php
namespace JetShop;

use Jet\Application_Module;
use Closure;
use Jet\Data_DateTime;
use Jet\Logger;
use JetApplication\EShop;
use JetApplication\SysServices;
use JetApplication\SysServices_PlannedOutage;

abstract class Core_SysServices_Definition {
	
	protected Application_Module $module;
	
	protected string $code = '';
	protected string $name = '';
	protected string $description = '';
	
	protected string $service_code = '';
	protected ?Closure $service = null;

	protected bool $service_requires_eshop_designation = false;

	protected bool $is_periodically_triggered_service = true;
	
	
	public function __construct( Application_Module $module, string $name, string $description, string $service_code, Closure $service )
	{
		$this->module = $module;
		$this->code = $module->getModuleManifest()->getName().':'.$service_code;
		
		$this->name = $name;
		$this->description = $description;
		
		$this->service_code = $service_code;
		$this->service = $service;
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
	
	public function getServiceCode(): string
	{
		return $this->service_code;
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
	
	

	public function getServiceRequiresEshopDesignation(): bool
	{
		return $this->service_requires_eshop_designation;
	}
	
	public function setServiceRequiresEshopDesignation( bool $service_requires_eshop_designation ): void
	{
		$this->service_requires_eshop_designation = $service_requires_eshop_designation;
	}
	

	public function getIsPeriodicallyTriggeredService(): bool
	{
		return $this->is_periodically_triggered_service;
	}
	
	public function setIsPeriodicallyTriggeredService( bool $is_periodically_triggered_service ): void
	{
		$this->is_periodically_triggered_service = $is_periodically_triggered_service;
	}
	
	
	
	public function getURL( ?EShop $eshop = null ) : string
	{
		return SysServices::getManager()->getSysServiceURL( $this, $eshop );
	}
	
	
	
	public function perform() : void
	{
		$this->service->call( $this->module );
	}
	
	/**
	 * @return SysServices_PlannedOutage[]
	 */
	public function getPlannedOutages() : array
	{
		return SysServices_PlannedOutage::getList( $this->code );
	}
	
	
	public function planOutage( ?Data_DateTime $from_date_time, ?Data_DateTime $till_date_time ) : void
	{
		if(!$from_date_time && !$till_date_time) {
			return;
		}
		
		SysServices_PlannedOutage::new( $this->code, $from_date_time, $till_date_time );
		
		Logger::info(
			event: 'sys_service:outage_scheduled',
			event_message: 'SysService'.$this->code.' outage scheduled',
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
					event: 'sys_service:scheduled_outage_cancelled',
					event_message: 'SysService'.$this->code.' scheduled outage cancelled',
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
		
		SysServices_PlannedOutage::new( $this->code, null, null );
		
		Logger::info(
			event: 'sys_service:shutdown',
			event_message: 'SysService '.$this->code.' shutdown',
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
				event: 'sys_service:start',
				event_message: 'SysService '.$this->code.' start',
				context_object_id: $this->code
			);
		}
		
	}
	
}