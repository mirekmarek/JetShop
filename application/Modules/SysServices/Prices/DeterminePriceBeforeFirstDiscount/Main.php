<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Prices\DeterminePriceBeforeFirstDiscount;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\Product_Price;
use JetApplication\Product_PriceHistory;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;



class Main extends Application_Module implements SysServices_Provider_Interface
{
	
	public function getSysServicesDefinitions(): array
	{
		$actualize_index = new SysServices_Definition(
			module: $this,
			name: Tr::_('Determine the price before the first discount'),
			description: Tr::_('to determine the price before the first discount according to the legislation'),
			service_code: 'actualize_pbfd',
			service: function() {
				foreach(Pricelists::getList() as $pricelist) {
					$this->determine( $pricelist );
				}
			}
		);
		
		$actualize_index->setIsPeriodicallyTriggeredService( true );
		
		return [
			$actualize_index
		];
	}
	
	public function determine( Pricelist $pricelist ) : void
	{
		echo "Pricelist: {$pricelist->getName()}\n";
		
		$prices = Product_Price::dataFetchAll(
			select: [
				'id',
				'entity_id',
				'price_before_discount',
				'price',
				'discount_percentage'
			],
			where: [
				'pricelist_code'=>$pricelist->getCode(),
				'AND',
				'set_discount_type' => ''
			],
			raw_mode: true
		);
		
		foreach($prices as $price) {
			$history = Product_PriceHistory::dataFetchAll(
				select: [
					'price',
					'date_time'
				],
				where: [
					'product_id' => $price['entity_id'],
					'AND',
					'pricelist_code' => $pricelist->getCode(),
				],
				order_by: ['date_time'],
				raw_mode: true
			);
			
			$prev_item = null;
			
			foreach($history as $index=>$h_item) {
				$history[$index]['price'] = $h_item['price'] = (float)$h_item['price'];
				
				if($prev_item===null) {
					$diff = 0;
				} else {
					$diff = $h_item['price'] - $prev_item['price'];
				}
				
				$prev_item = $h_item;
				
				$history[$index]['diff'] = $diff;
			}
			
			
			$first_relevant_discount = null;
			$last_relevant_discount = null;
			
			foreach($history as $h_item) {
				$diff = $h_item['diff'];
				
				/*
				if(abs($diff)<=0.1) {
					continue;
				}
				*/
				
				if($diff>0) {
					$first_relevant_discount = null;
					$last_relevant_discount = null;
					continue;
				}
				
				if($first_relevant_discount===null) {
					$first_relevant_discount = $h_item;
				}
				
				$last_relevant_discount = $h_item;
			}
			
			
			$price_before_first_discount = 0.0;
			
			$last_relevant_discount_date = null;
			
			if( $last_relevant_discount ) {
				
				$price_before_first_discount = $first_relevant_discount['price']+(-1*$first_relevant_discount['diff']);
				$last_relevant_discount_date = $last_relevant_discount['date_time'];
				
				$deadline = strtotime('-30 days');
				if(strtotime($last_relevant_discount_date)<$deadline) {
					$price_before_first_discount = 0.0;
					$last_relevant_discount_date = null;
				}
				
			}
			
			
			if($price['price_before_discount']==$price_before_first_discount) {
				continue;
			}
			
			if(!$price_before_first_discount) {
				$price['price_before_discount'] = 0;
				$price['discount_percentage'] = 0;
			} else {
				$price['price_before_discount'] = $price_before_first_discount;
				$price['discount_percentage'] = 100-( ($price['price'] * 100) / $price_before_first_discount);
			}
			
			Product_Price::updateData(
				data: [
					'price_before_discount' => $price['price_before_discount'],
					'discount_percentage' => $price['discount_percentage']
				],
				where: [
					'id' => $price['id']
				]
			);
			
			
		}
		
	}
	

}