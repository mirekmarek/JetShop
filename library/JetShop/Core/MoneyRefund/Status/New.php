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
use JetApplication\EShopEntity_Status_PossibleFutureState;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event;
use JetApplication\MoneyRefund_Event_NewRequest;
use JetApplication\MoneyRefund_Status;
use JetApplication\MoneyRefund_Status_Cancelled;
use JetApplication\MoneyRefund_Status_InProcessing;

abstract class Core_MoneyRefund_Status_New extends MoneyRefund_Status {
	
	public const CODE = 'new';
	
	public function __construct()
	{
		$this->title = Tr::_('New', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 10;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		return 'background-color: #00ddc1;';
	}
	
	
	public function createEvent( EShopEntity_Basic|MoneyRefund $item, string $previouse_status_code ): null|EShopEntity_Event|MoneyRefund_Event
	{
		return $item->initEvent( MoneyRefund_Event_NewRequest::new() );
	}
	
	public function getPossibleFutureStates(): array
	{
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureState {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Start processing') )
					->setClass( UI_button::CLASS_PRIMARY );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return MoneyRefund_Status_InProcessing::get();
			}
		};
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureState {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Cancel') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return MoneyRefund_Status_Cancelled::get();
			}
		};
		
		return $res;
	}
	
}