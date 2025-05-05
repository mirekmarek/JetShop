<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint_ComplaintType;

abstract class Core_Complaint_ComplaintType_PostWarrantyClaim extends Complaint_ComplaintType
{
	public const CODE = 'post_warranty_claim';
	protected string $title = 'Post-warranty claims / spare parts';
	protected int $priority = 20;
}