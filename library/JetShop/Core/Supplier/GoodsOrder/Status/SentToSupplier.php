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
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\Supplier_GoodsOrder_Event_SentToSupplier;
use JetApplication\Supplier_GoodsOrder_Status;
use JetApplication\Supplier_GoodsOrder_Status_Cancelled;
use JetApplication\Supplier_GoodsOrder_Status_SentToSupplier;

abstract class Core_Supplier_GoodsOrder_Status_SentToSupplier extends Supplier_GoodsOrder_Status {
	
	public const CODE = 'sent_to_supplier';
	protected string $title = 'Sent to the supplier';
	protected int $priority = 60;
	protected bool $sent_to_the_supplier = true;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$statuses = [];
		
		
		$statuses[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button(Tr::_('Send to the supplier again'))->setClass(UI_button::CLASS_INFO);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Supplier_GoodsOrder_Status_SentToSupplier::get();
			}
		};
		
		
		$statuses[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_( 'Cancel' ) )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Supplier_GoodsOrder_Status_Cancelled::get();
			}
		};
		
		return $statuses;

	}
	
	
	public function createEvent( EShopEntity_Basic|Supplier_GoodsOrder $item, EShopEntity_Status|Supplier_GoodsOrder_Status $previouse_status ): ?EShopEntity_Event
	{
		return $item->createEvent( new Supplier_GoodsOrder_Event_SentToSupplier() );
	}
	
}