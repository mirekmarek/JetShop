<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_TimePeriod_PreviousYear extends Report_TimePeriod
{
	public const KEY = 'previous_year';
	protected ?string $title = 'Previous year';
	
	public function __construct()
	{
		$this->setupFrom('last year January 1st');
		$this->setupTo('last year December 31st');
	}
}