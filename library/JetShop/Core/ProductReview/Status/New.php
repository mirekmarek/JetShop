<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\ProductReview;
use JetApplication\ProductReview_Event;
use JetApplication\ProductReview_Event_New;
use JetApplication\ProductReview_Status;
use JetApplication\ProductReview_Status_Approved;
use JetApplication\ProductReview_Status_Rejected;

abstract class Core_ProductReview_Status_New extends ProductReview_Status {
	
	public const CODE = 'new';
	protected string $title = 'New';
	protected int $priority = 10;
	
	protected static array $flags_map = [
		'assessed' => false,
		'approved' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function createEvent( EShopEntity_Basic|ProductReview $item, EShopEntity_Status $previouse_status ): ?ProductReview_Event
	{
		return $item->createEvent( ProductReview_Event_New::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = ProductReview_Status_Approved::getAsPossibleFutureStatus();
		$res[] = ProductReview_Status_Rejected::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}