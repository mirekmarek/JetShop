<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Status;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status extends EShopEntity_Status {

	protected static string $base_status_class = ReturnOfGoods_Status::class;
	
	protected static array $flags_map = [
		'cancelled' => null,
		
		'completed' => null,
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
	];
	
	protected static ?array $list = null;
}