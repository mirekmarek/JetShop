<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataListing;
use Jet\DataListing_Column;
use Jet\DataModel_Fetch_Instances;
use Jet\Http_Request;
use Jet\Locale;
use Jet\MVC_View;
use Jet\Tr;
use Jet\UI;
use Jet\UI_badge;

class Report_Customer_Sessions extends Report_Customer
{
	public const KEY = 'sessions';
	protected ?string $title = 'Sessions - activity';
	protected int $priority = 1;
	protected bool $is_default = true;
	protected array $sub_reports = [
		'session_details' => 'Session details',
		'summary' => 'Summary',
	];
	
	public function prepare_summary() : void
	{
		//TODO:
	}
	
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
	
			$listing = new class($this) extends DataListing {
				protected Report_Customer_Sessions $report;
				

				public function __construct( Report_Customer_Sessions $report )
				{
					$this->report = $report;
				}
				
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
				
				
				protected function getItemList(): DataModel_Fetch_Instances
				{
					return Session::fetchInstances();
				}
				
				protected function getIdList(): array
				{
					return [];
				}
				
				public function getFilterView(): MVC_View
				{
					return $this->report->getView();
				}
				
				public function getColumnView(): MVC_View
				{
					return $this->report->getView();
				}
				
				public function itemGetter( int|string $id ): Session
				{
					return Session::load([
						'id' => $id,
						'AND',
						'customer_id' => $this->report->getCustomer()->getId(),
					]);
				}
			};
			
			$listing->addColumn( new class extends DataListing_Column {
				public function getKey(): string { return 'id'; }
				public function getTitle(): string { return Tr::_('Session ID'); }
				public function render( mixed $item ) : string
				{
					/**
					 * @var Session $item
					 */
					return '<a href="'.Http_Request::currentURI(['session_id'=>$item->getId()]).'">'.$item->getId().'</a>';
				}
			} );
			
			$listing->addColumn( new class extends DataListing_Column {
				public function getKey(): string { return 'start_date_time'; }
				public function getTitle(): string { return Tr::_('Session start'); }
				public function render( mixed $item ) : string
				{
					/**
					 * @var Session $item
					 */
					return '<a href="'.Http_Request::currentURI(['session_id'=>$item->getId()]).'">'.Locale::dateAndTime( $item->getStartDateTime(), Locale::DATE_TIME_FORMAT_SHORT ).'</a>';
				}
			} );
			
			
			$listing->addColumn( new class extends DataListing_Column {
				public function getKey(): string { return 'last_activity_date_time'; }
				public function getTitle(): string { return Tr::_('Last activity'); }
				public function render( mixed $item ) : string
				{
					/**
					 * @var Session $item
					 */
					return '<a href="'.Http_Request::currentURI(['session_id'=>$item->getId()]).'">'.Locale::dateAndTime( $item->getLastActivityDateTime(), Locale::DATE_TIME_FORMAT_SHORT ).'</a>';
				}
			} );
			
			
			$listing->addColumn( new class extends DataListing_Column {
				public function getKey(): string { return 'first_page_URL'; }
				public function getTitle(): string { return Tr::_('The first visited page'); }
				public function render( mixed $item ) : string
				{
					/**
					 * @var Session $item
					 */
					return '<a href="'.$item->getFirstPageURL().'" target="_blank">'.UI::icon('link').'</a> '.$item->getFirstPageURL();
				}
			} );
			
			$listing->addColumn( new class extends DataListing_Column {
				public function getKey(): string { return 'last_page_URL'; }
				public function getTitle(): string { return Tr::_('The last visited page'); }
				public function render( mixed $item ) : string
				{
					/**
					 * @var Session $item
					 */
					return '<a href="'.$item->getLastPageURL().'" target="_blank">'.UI::icon('link').'</a> '.$item->getLastPageURL();
				}
			} );
			
			$listing->addColumn( new class extends DataListing_Column {
				public function getKey(): string { return 'purchased'; }
				public function getTitle(): string { return Tr::_('Purchased ?'); }
				public function render( mixed $item ) : string
				{
					/**
					 * @var Session $item
					 */
					return $item->isPurchased() ?
						UI::badge( UI_badge::SUCCESS, Tr::_('Yes') )
						:
						UI::badge( UI_badge::INFO, Tr::_('No') );
				}
			} );
			
			$listing->addColumn( new class extends DataListing_Column {
				public function getKey(): string { return 'shopping_cart_used'; }
				public function getTitle(): string { return Tr::_('Shopping cart used ?'); }
				public function render( mixed $item ) : string
				{
					/**
					 * @var Session $item
					 */
					return $item->isShoppingCartUsed() ?
						UI::badge( UI_badge::SUCCESS, Tr::_('Yes') )
						:
						UI::badge( UI_badge::INFO, Tr::_('No') );
				}
			} );
			
			
			
			$listing->setDefaultSort('-id');
			
			
			$listing->handle();
			$listing->getVisibleColumns();
			
			
			$grid = $listing->getGrid();
			
			
			$this->view->setVar('grid', $grid);
			$this->view->setVar('listing', $listing);
		}
		
		

	}

}