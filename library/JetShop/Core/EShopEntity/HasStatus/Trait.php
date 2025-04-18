<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Closure;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Logger;
use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_VirtualStatus;

trait Core_EShopEntity_HasStatus_Trait {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $status = '';
	
	public function getFlags() : array
	{
		$res = [];
		foreach( static::$flags as $flag ) {
			$res[$flag] = $this->{$flag};
		}
		
		return $res;
	}
	
	public function setFlags( array $flags ) : void
	{
		$updated = [];
		foreach( static::$flags as $flag ) {
			if(
				!array_key_exists($flag, $flags) ||
				$flags[$flag] === null
			) {
				continue;
			}
			
			$this->{$flag} = (bool)$flags[$flag];
			$updated[$flag] = (bool)$flags[$flag];
		}
		
		if($updated) {
			static::updateData(
				data: $updated,
				where: [
					'id' => $this->id
				]
			);
		}
	}
	
	public function setStatus(
		EShopEntity_Status|EShopEntity_VirtualStatus $status,
		bool $handle_event=true,
		array $params=[],
		?Closure $event_setup=null
	) : void
	{
		if( $status instanceof EShopEntity_VirtualStatus ) {
			$status::handle( $this, $handle_event, $params, $event_setup );
			return;
		}
		
		if( $this->status == $status->getCode() ) {
			return;
		}
		
		$prev_status = $this->getStatus();
		
		$this->status = $status->getCode();
		
		static::updateData(
			data: [
				'status' => $this->status,
			],
			where: [
				'id' => $this->id
			]
		);
		
		$this->setFlags( $status::getFlagsMap() );
		
		$status->setupObjectAfterStatusUpdated( $this, $params );
		
		Logger::info(
			event: static::getEntityType().':status_updated',
			event_message: 'Status updated. New status: '.$status->getCode(),
			context_object_id: $this->id,
			context_object_data: $this
		);
		
		
		if(
			$this instanceof EShopEntity_HasEvents_Interface &&
			$handle_event &&
			($event=$status->createEvent( $this, $prev_status ))
		) {
			if($event_setup) {
				$event_setup( $event );
			}
			
			$event->handleImmediately();
		}
	}
	
	public function getStatus() : EShopEntity_Status
	{
		$status = static::getStatusList()[$this->status]??null;
		if($status) {
			return $status;
		}
		
		if(!$this->status) {
			$status = $this->setStatusByFlagState( handle_event: false );
			if($status) {
				return $status;
			}
		}
		
		return new class extends EShopEntity_Status
		{
			public function getTitle() : string
			{
				return Tr::_( 'Unknown status', dictionary: Tr::COMMON_DICTIONARY );
			}
			
			public function getShowAdminCSSClass(): string
			{
				return 'status-warning';
			}
			
			public function createEvent( EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?EShopEntity_Event
			{
				return null;
			}
			
			public function getPossibleFutureStatuses(): array
			{
				return [];
			}
		};

	}
	
	public function setStatusByFlagState( bool $set=true, bool $handle_event=true ) : ?EShopEntity_Status
	{
		foreach(static::getStatusList() as $status) {
			if($status::resolve( $this )) {
				if($status->getCode()==$this->status) {
					return $status;
				}
				
				$this->status = $status->getCode();
				
				if($set) {
					$this->setStatus( $status, $handle_event );
				}
				
				return $status;
			}
		}
		
		return null;
	}
	
	
}