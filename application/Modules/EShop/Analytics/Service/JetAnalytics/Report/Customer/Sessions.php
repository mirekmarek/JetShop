<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_Customer_Sessions extends Report_Customer
{
	public const KEY = 'sessions';
	protected ?string $title = 'Sessions - activity';
	protected bool $is_default = true;
	protected array $sub_reports = [
		'summary' => 'Summary',
		'session_details' => 'Session details',
	];
	
	public function prepare_summary() : void
	{
		//TODO:
	}
	
	public function prepare_session_details() : void
	{
		//TODO:
	}

}