<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_TimePeriod_Last7Days extends Report_TimePeriod
{
	public const KEY = 'Last_7_days';
	protected ?string $title = 'Last 7 days';
	
	public function __construct()
	{
		$this->setupFrom('-7 days');
		$this->setupTo('now');
	}
}