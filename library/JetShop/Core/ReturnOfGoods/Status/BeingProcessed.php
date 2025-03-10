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
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureState;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\ReturnOfGoods_Status;
use JetApplication\ReturnOfGoods_Status_Cancelled;
use JetApplication\ReturnOfGoods_Status_ClarificationRequired;
use JetApplication\ReturnOfGoods_Status_Rejected;

abstract class Core_ReturnOfGoods_Status_BeingProcessed extends ReturnOfGoods_Status {
	
	public const CODE = 'being_processed';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => null,
		'being_processed' => true,
		
		'rejected' => false,
		
		'accepted' => false,
		
		'money_refund' => false,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Being processed', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 30;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function getPossibleFutureStates(): array
	{
		$res = [];
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureState {
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Clarification required') )
					->setClass( UI_button::CLASS_PRIMARY );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ReturnOfGoods_Status_ClarificationRequired::get();
			}
			
			public function noteForCustomerEnabled() : bool
			{
				return true;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return true;
			}
		};
		
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureState {
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Rejected') )
					->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ReturnOfGoods_Status_Rejected::get();
			}
			
			public function noteForCustomerEnabled() : bool
			{
				return true;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return true;
			}
		};
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureState {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Cancel') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ReturnOfGoods_Status_Cancelled::get();
			}
			
			public function noteForCustomerEnabled() : bool
			{
				return true;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return true;
			}
			
		};
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureState {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Cancel') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ReturnOfGoods_Status_Cancelled::get();
			}
			
			public function noteForCustomerEnabled() : bool
			{
				return true;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return true;
			}
			
		};
		
		
		return $res;
	}
	
	
}