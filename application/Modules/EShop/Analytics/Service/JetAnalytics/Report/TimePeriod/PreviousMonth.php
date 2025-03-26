<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_TimePeriod_PreviousMonth extends Report_TimePeriod
{
	public const KEY = 'previous_month';
	protected ?string $title = 'Previous month';
	
	public function __construct()
	{
		$this->setupFrom('first day of last month');
		$this->setupTo('last day of last month');
	}
}