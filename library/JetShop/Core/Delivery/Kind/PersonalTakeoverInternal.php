<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Delivery_Kind;

abstract class Core_Delivery_Kind_PersonalTakeoverInternal extends Delivery_Kind
{
	public const CODE = 'personal-takeover-internal';
	protected string $title = 'Personal takeover - internal';
	protected int $priority = 30;
	protected bool $module_is_required = true;
	protected bool $is_personal_takeover = true;
	protected bool $is_personal_takeover_internal = true;
	
}