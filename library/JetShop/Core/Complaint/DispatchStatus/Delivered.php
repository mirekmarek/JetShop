<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Complaint_DispatchStatus;

abstract class Core_Complaint_DispatchStatus_Delivered extends Complaint_DispatchStatus {
	
	public const CODE = 'delivered';
	
	protected static array $flags_map = [
		'ready_for_dispatch' => true,
		'dispatch_started' => true,
		
		'dispatched' => true,
		'delivered' => true,
		'returned' => false,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Delivered', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 40;
	}
	
}