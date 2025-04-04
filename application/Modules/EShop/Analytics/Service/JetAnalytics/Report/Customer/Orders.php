<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_Customer_Orders extends Report_Customer
{
	public const KEY = 'orders';
	protected ?string $title = 'Orders';
	protected int $priority = 2;
	protected bool $is_default = false;
	protected array $sub_reports = [
		'summary' => 'Summary'
	];
	
	public function prepare_summary() : void
	{
		//TODO:
	}

}