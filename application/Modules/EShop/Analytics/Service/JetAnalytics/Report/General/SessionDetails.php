<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Headers;
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
	protected array $filter = [
		'what' => '',
	];
	
	public function getFilter(): array
	{
		return $this->filter;
	}
	
	
	
	public function prepare_session_details() : void
	{
		
		
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
			$what_options = [
				'' => Tr::_('- all -'),
				'purchased' => Tr::_('Purchased'),
				'shopping_cart_used' => Tr::_('Shopping cart used, not purchased'),
				'no_shopping' => Tr::_('No shopping'),
			];
			$this->filter['what'] = Http_Request::GET()->getString('what', default_value: '', valid_values: array_keys($what_options));
			
			$source_options = [
				'' => Tr::_('- all -'),
			];
			foreach(Session_SourceDetector::getSources() as $source) {
				$source_options[$source] = $source;
			}
			
			$this->filter['source'] = Http_Request::GET()->getString('source', default_value: '', valid_values: array_keys($source_options));
			
			
			
			$filter_what = new Form_Field_Select('what', 'What:');
			$filter_what->setDefaultValue( $this->filter['what'] );
			$filter_what->setSelectOptions( $what_options );
			
			$filter_source = new Form_Field_Select('source', 'Source:');
			$filter_source->setDefaultValue( $this->filter['source'] );
			$filter_source->setSelectOptions( $source_options );
			
			$filter_form = new Form('filter_form', [
				$filter_what,
				$filter_source
			]);
			
			if($filter_form->catch()) {
				$f = [];
				foreach($filter_form->getFields() as $field) {
					$f[$field->getName()] = $field->getValue();
				}
				
				Http_Headers::reload( set_GET_params: $f );
			}
			
			
			
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
					
					switch($filter['what']) {
						case 'purchased':
							$where[] = 'AND';
							$where[] = [
								'purchased' => true
							];
							break;
						case 'shopping_cart_used':
							$where[] = 'AND';
							$where[] = [
								'purchased' => false,
								'AND',
								'shopping_cart_used' => true
							];
							break;
						case 'no_shopping':
							$where[] = 'AND';
							$where[] = [
								'purchased' => false,
								'AND',
								'shopping_cart_used' => false
							];
							break;
					}
					
					if($filter['source']) {
						$where[] = 'AND';
						$where['source'] = $filter['source'];
					}
					
					return $where;
				}
			};
			
			$this->view->setVar( 'filter_form', $filter_form );
			
			
			$this->view->setVar( 'listing', $listing );
		}
		
	}
}