<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Http_Request;
use Jet\Tr;

class Report_General_SessionDetails extends Report_General
{
	public const KEY = 'session_details';
	protected ?string $title = 'Session details';
	protected bool $is_default = true;
	protected bool $one_eshop_mode = true;
	protected array $sub_reports = [
		'session_details' => 'Session details',
	];
	protected string $filter = '';
	
	public function getFilter(): string
	{
		return $this->filter;
	}
	
	
	
	public function prepare_session_details() : void
	{
		$filter_options = [
			'' => Tr::_('- all -'),
			'purchased' => Tr::_('Purchased'),
			'shopping_cart_used' => Tr::_('Shopping cart used, not purchased'),
			'no_shopping' => Tr::_('No shopping'),
		];
		
		$this->filter = Http_Request::GET()->getString('filter', default_value: '', valid_values: array_keys($filter_options));
		
		
		$session = null;
		$session_id = Http_Request::GET()->getInt('session_id');
		if($session_id) {
			$session = Session::load([
				'id' => $session_id,
				'AND',
				$this->selected_eshop->getWhere()
			]);
			
			if($session) {
				$this->view->setVar('session', $session);
			}
		}
		
		
		if(!$session) {
			
			$listing = new class( $this ) extends Report_SessionListing {
				protected function getDefaultFilterWhere() : array
				{
					$where = [
						[
							'start_date_time >=' => $this->report->getDateFrom(),
							'AND',
							'start_date_time <=' => $this->report->getDateTo(),
						],
						'AND',
						$this->report->getSelectedEshop()->getWhere()
					];
					
					$filter = $this->report->getFilter();
					
					if($filter) {
						$where[] = 'AND';
						switch($filter) {
							case 'purchased':
								$where[] = [
									'purchased' => true
								];
								break;
							case 'shopping_cart_used':
								$where[] = [
									'purchased' => false,
									'AND',
									'shopping_cart_used' => true
								];
								break;
							case 'no_shopping':
								$where[] = [
									'purchased' => false,
									'AND',
									'shopping_cart_used' => false
								];
								break;
						}
					}
					
					return $where;
				}
			};
			
			$this->view->setVar( 'filter_options', $filter_options );
			$this->view->setVar( 'filter', $this->getFilter() );
			$this->view->setVar( 'listing', $listing );
		}
		
	}
}