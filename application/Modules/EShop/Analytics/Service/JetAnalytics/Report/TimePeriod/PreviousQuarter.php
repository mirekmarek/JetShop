<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_TimePeriod_PreviousQuarter extends Report_TimePeriod
{
	public const KEY = 'previous_quarter';
	protected ?string $title = 'Previous quarter';
	
	public function __construct()
	{
		$this->setupFrom('-3 months');
		$this->from->setDate( $this->from->format('Y'), $this->from->format('m'), 1 );
		$this->setupTo('last day of last month');
	}
}