<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use Jet\UI;
use Jet\UI_button;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\ProductReview;
use JetApplication\ProductReview_Event;
use JetApplication\ProductReview_Event_Approved;
use JetApplication\ProductReview_Status;
use JetApplication\ProductReview_Status_Approved;

abstract class Core_ProductReview_Status_Approved extends ProductReview_Status {
	
	public const CODE = 'approved';
	protected string $title = 'Approved';
	protected int $priority = 20;
	
	protected static array $flags_map = [
		'assessed' => true,
		'approved' => true,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( EShopEntity_Basic|ProductReview $item, EShopEntity_Status $previouse_status ): ?ProductReview_Event
	{
		return $item->createEvent( ProductReview_Event_Approved::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Approve') )->setClass( UI_button::CLASS_SUCCESS );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ProductReview_Status_Approved::get();
			}
			
		};
	}
	
}