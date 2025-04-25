<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Delivery_Kind;

abstract class Core_Delivery_Kind_PersonalTakeoverExternal extends Delivery_Kind
{
	public const CODE = 'personal-takeover-external';
	protected string $title = 'Personal takeover - external';
	protected int $priority = 20;
	protected bool $module_is_required = true;
	protected bool $is_personal_takeover = true;
}