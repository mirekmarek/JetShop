<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Http_Request;

class Report_Customer_Sessions extends Report_Customer
{
	public const KEY = 'sessions';
	protected ?string $title = 'Sessions - activity';
	protected int $priority = 1;
	protected bool $is_default = true;
	protected array $sub_reports = [
		'session_details' => 'Session details',
	];
	
	
	public function prepare_session_details() : void
	{
		$session = null;
		$session_id = Http_Request::GET()->getInt('session_id');
		if($session_id) {
			$session = Session::load([
				'id' => $session_id,
				'AND',
				'customer_id' => $this->customer->getId(),
			]);
			
			if($session) {
				$this->view->setVar('session', $session);
			}
		}
		
		
		if(!$session) {
	
			$listing = new class($this) extends Report_SessionListing {
				protected function getDefaultFilterWhere() : array
				{
					return [
						[
							'start_date_time >=' => $this->report->getDateFrom(),
							'AND',
							'start_date_time <=' => $this->report->getDateTo(),
						],
						'AND',
						'customer_id' => $this->report->getCustomer()->getId()
					];
				}
			};
			
			
			$this->view->setVar('listing', $listing);
		}
		
		

	}

}