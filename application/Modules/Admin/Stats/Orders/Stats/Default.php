<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Stats\Orders;


use JetApplication\Statistics_Order;

class Stats_Default extends Statistics_Order {
	public const KEY = 'default';
	
	protected string $title = 'Default';
	
	
}