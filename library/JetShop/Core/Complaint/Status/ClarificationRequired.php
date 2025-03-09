<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Complaint_Status;

abstract class Core_Complaint_Status_ClarificationRequired extends Complaint_Status {
	
	public const CODE = 'clarification_required';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => false,
		
		'being_processed' => true,
		'clarification_required' => true,
		
		'accepted' => false,
		'money_refund' => false,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => false,
		
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Clarification required', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 40;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		return 'background-color: #00ddc1;';
	}
	
}