<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Closure;
use Jet\Tr;
use Jet\UI;
use Jet\UI_button;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\Supplier_GoodsOrder_Status_SentToSupplier;
use JetApplication\Supplier_GoodsOrder_VirtualStatus;

abstract class Core_Supplier_GoodsOrder_VirtualStatus_SendAgain extends Supplier_GoodsOrder_VirtualStatus {
	
	public const CODE = 'send_again';
	protected string $title = 'Send to the supplier again';
	
	public function getTitle(): string
	{
		return $this->title;
	}
	
	public static function handle( EShopEntity_HasStatus_Interface $item, bool $handle_event = true, array $params = [], ?Closure $event_setup = null ): void
	{
	}
	
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button(Tr::_('Send to the supplier again'))->setClass(UI_button::CLASS_SUCCESS);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Supplier_GoodsOrder_Status_SentToSupplier::get();
			}
			
		};
	}
	
}