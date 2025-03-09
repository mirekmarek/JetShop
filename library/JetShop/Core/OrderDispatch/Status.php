<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = OrderDispatch_Status::class;
	
	protected static array $flags_map = [];
	
	protected static bool $is_editable = false;
	protected static bool $is_in_progress = false;
	protected static bool $is_prepared = false;
	protected static bool $is_ready_to_create_consignment = false;
	protected static bool $is_consignment_created = false;
	protected static bool $can_create_consignment = false;
	protected static bool $is_rollback_possible = false;
	protected static bool $can_be_cancelled = false;
	
	protected static bool $is_sent = false;
	
	protected static ?array $list = null;
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, string $previouse_status_code ): null|EShopEntity_Event|OrderDispatch_Event
	{
		return null;
	}
	
	public static function isEditable(): bool
	{
		return static::$is_editable;
	}
	
	public static function isInProgress(): bool
	{
		return static::$is_in_progress;
	}
	
	public static function isPrepared(): bool
	{
		return static::$is_prepared;
	}
	
	public static function isSent(): bool
	{
		return static::$is_sent;
	}
	
	public static function isReadyToCreateConsignment() : bool
	{
		return static::$is_ready_to_create_consignment;
	}
	
	public static function isConsignmentCreated() : bool
	{
		return static::$is_consignment_created;
	}
	
	public static function canCreateConsignment(): bool
	{
		return static::$can_create_consignment;
	}
	
	public static function isRollbackPossible(): bool
	{
		return static::$is_rollback_possible;
	}
	
	public static function canBeCancelled(): bool
	{
		return static::$can_be_cancelled;
	}
	
	
	
	public static function getInProgressStatusCodes(): array
	{
		$res = [];
		
		foreach( static::getList() as $status ) {
			if( $status::isInProgress() ) {
				$res[] = $status::getCode();
			}
		}
		
		return $res;
	}
	
	public static function getPreparedStatusCodes(): array
	{
		$res = [];
		foreach( static::getList() as $status ) {
			if( $status::isPrepared() ) {
				$res[] = $status::getCode();
			}
		}
		
		return $res;
	}
	
	public static function getSentStatusCodes(): array
	{
		$res = [];
		foreach( static::getList() as $status ) {
			if( $status::isSent() ) {
				$res[] = $status::getCode();
			}
		}
		
		return $res;
	}
	
}