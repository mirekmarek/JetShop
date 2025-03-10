<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Event_DoneRejected;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status_Rejected extends ReturnOfGoods_Status {
	
	public const CODE = 'rejected';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => true,
		
		'accepted' => false,
		
		'money_refund' => false,
	];

	
	public function __construct()
	{
		$this->title = Tr::_('Rejected', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 50;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
	public function createEvent( EShopEntity_Basic|ReturnOfGoods $item, EShopEntity_Status $previouse_status ): ?ReturnOfGoods_Event
	{
		return $item->initEvent( ReturnOfGoods_Event_DoneRejected::new() );
	}
	
}