<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Stats\Orders;

use Jet\Tr;
use JetApplication\Order;
use JetApplication\Statistics_Order;
use JetApplication\Statistics_Order_Result;

class Stats_ByOrderSource extends Statistics_Order {
	public const KEY = 'by_order_source';
	
	protected string $title = 'By order source';
	
	public function prepareResults() : void
	{
		$this->setWhere([]);
		$result = new Statistics_Order_Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		$result->setTitle( Tr::_('All orders') );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
		
		
		$this->setWhere([
			'import_source' => ''
		]);
		$result = new Statistics_Order_Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		$result->setTitle( Tr::_('Internal orders') );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
		
		$sources = Order::dataFetchCol(['import_source'], where: ['import_source !='=>''], group_by: ['import_source']);
		
		foreach($sources as $source) {
			$this->setWhere([
				'import_source' => $source
			]);
			$result = new Statistics_Order_Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
			$result->setTitle( $source );
			$result->setData( $this->getRawData() );
			$this->results[] = $result;
		}
		
	}
	
}