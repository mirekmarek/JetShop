<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Complaint_Status;

abstract class Core_Complaint_Status_Incomplete extends Complaint_Status {
	
	public const CODE = 'incomplete';
	
	protected static array $flags_map = [
		'completed' => false,
		'cancelled' => false,
		
		'clarification_required' => null,
		'being_processed' => null,
		'rejected' => null,
		'accepted' => null,
		'money_refund' => null,
		'sent_for_repair' => null,
		'repaired' => null,
		'send_new_products' => null,
		
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Incomplete', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 10;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		return 'background-color: #ffaaaaaa;color: #111111;';
	}
	
}