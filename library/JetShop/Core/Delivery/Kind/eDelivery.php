<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Delivery_Kind;

abstract class Core_Delivery_Kind_eDelivery extends Delivery_Kind
{
	public const CODE = 'e-delivery';
	protected string $title = 'e-Delivery';
	protected int $priority = 90;
}