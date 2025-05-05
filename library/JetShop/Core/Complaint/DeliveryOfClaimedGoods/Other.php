<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint_DeliveryOfClaimedGoods;

abstract  class Core_Complaint_DeliveryOfClaimedGoods_Other extends Complaint_DeliveryOfClaimedGoods {
	
	public const CODE = 'other';
	protected string $title = 'Other';
	protected int $priority = 40;
}