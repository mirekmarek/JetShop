<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Complaint_Status;

abstract class Core_Complaint_Status_AcceptedNewProduct extends Complaint_Status {
	
	public const CODE = 'accepted_new_product';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => false,
		
		'clarification_required' => null,
		'being_processed' => null,
		
		'accepted' => true,
		
		'money_refund' => false,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => true,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Accepted - New products', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 100;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		return 'background-color: #9faaFF;color: #111111;';
	}
	
}