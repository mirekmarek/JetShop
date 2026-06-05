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

class Report_SessionListing extends DataListing {
	protected Report $report;
	
	
	public function __construct( Report $report )
	{
		$this->report = $report;
		
		$this->initColumns();
		
		$this->handle();
		$this->getVisibleColumns();
		
	}
	
	protected function getItemList(): DataModel_Fetch_Instances
	{
		return Session::fetchInstances();
	}
	
	protected function getIdList(): array
	{
		return Session::dataFetchCol(
			select: ['id'],
			where: $this->getFilterWhere(),
			order_by: $this->getQueryOrderBy()
		);
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
			'id' => $id
		]);
	}
	
	public function initColumns() : void
	{
		$this->addColumn( new class extends DataListing_Column {
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
		
		$this->addColumn( new class extends DataListing_Column {
			public function getKey(): string { return 'source'; }
			public function getTitle(): string { return Tr::_('Source'); }
			public function render( mixed $item ) : string
			{
				/**
				 * @var Session $item
				 */
				return $item->getSource();
			}
		} );
		
		
		$this->addColumn( new class extends DataListing_Column {
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
		
		$this->addColumn( new class extends DataListing_Column {
			public function getKey(): string { return 'checkout_started'; }
			public function getTitle(): string { return Tr::_('Checkout started ?'); }
			public function render( mixed $item ) : string
			{
				/**
				 * @var Session $item
				 */
				return $item->getCheckoutStarted() ?
					UI::badge( UI_badge::SUCCESS, Tr::_('Yes') )
					:
					UI::badge( UI_badge::INFO, Tr::_('No') );
			}
		} );
		
		$this->addColumn( new class extends DataListing_Column {
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
		
		
		$this->addColumn( new class extends DataListing_Column {
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
		
		
		$this->addColumn( new class extends DataListing_Column {
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
		
		$this->addColumn( new class extends DataListing_Column {
			public function getKey(): string { return 'session_duration'; }
			public function getTitle(): string { return Tr::_('Session duration'); }
			public function render( mixed $item ) : string
			{
				/**
				 * @var Session $item
				 */
				return $item->getSessionDuration( true );
			}
		} );
		
		
		
		$this->setDefaultSort('-id');
		
	}

}