<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Stats\Orders;


use Jet\Tr;
use JetApplication\Delivery_Method;

class Stat_ByDeliveryMethod extends Stat {
	public const KEY = 'by_delivery_method';
	
	protected string $title = 'By delivery method';
	
	public function prepareResults() : void
	{
		$this->setWhere([
			'import_source' => ''
		]);
		$result = new Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		$result->setTitle( Tr::_('All internal orders') );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
		
		
		foreach( Delivery_Method::getScope() as $id=>$name ) {
			$this->setWhere([
				'import_source' => '',
				'AND',
				'delivery_method_id' => $id
			]);
			$result = new Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
			$result->setTitle( $name );
			$result->setData( $this->getRawData() );
			$this->results[] = $result;
			
		}
		
		uasort( $this->results, function(
			Result $a,
			Result $b
		) {
			$a_val = $a->getYearData( $a->getEndYear() )->getAmount(false);
			$b_val = $b->getYearData( $a->getEndYear() )->getAmount(false);
			
			if( $a_val==$b_val ) {
				return 0;
			}
			
			return ($a_val > $b_val) ? -1 : 1;
		} );
		
		
	}
	
}