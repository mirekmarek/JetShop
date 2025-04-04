<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Stats\Orders;


use Jet\Tr;

class Stat_RegisteredCustomer extends Stat {
	public const KEY = 'registered_customer';
	
	protected string $title = 'Registered / not registered customers';
	
	public function prepareResults() : void
	{
		$this->setWhere([
			'import_source' => ''
		]);
		$result = new Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		$result->setTitle( Tr::_('All internal orders') );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
		
		
		$this->setWhere([
			'import_source' => '',
			'AND',
			'customer_id >' => 0,
		]);
		$result = new Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		$result->setTitle( Tr::_('Registered customers') );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
		
		
		$this->setWhere([
			'import_source' => '',
			'AND',
			'customer_id' => 0,
		]);
		$result = new Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		$result->setTitle( Tr::_('Not registered customers') );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
		
	}
	
}