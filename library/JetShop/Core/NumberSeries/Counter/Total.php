<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\NumberSeries_Counter;
use JetApplication\NumberSeries_Entity_Interface;

#[DataModel_Definition(
name: 'number_series_counter_total',
	database_table_name: 'number_series_counter_total',
)]
abstract class Core_NumberSeries_Counter_Total extends NumberSeries_Counter
{
	
	public static function generate( NumberSeries_Entity_Interface $entity, int $pad ) : string
	{
		$where = [
			'entity' => $entity->getNumberSeriesEntityType()
		];
		if( ($shop=$entity->getNumberSeriesEntityShop()) ) {
			$where[] = 'AND';
			$where['shop_key'] = $shop->getKey();
		}
		
		
		$current = static::dataFetchAll(
			select: ['count'],
			where: $where
		);
		
		foreach($current as $item ) {
			$count = $item['count'];
			$count++;
			
			static::updateData(
				data: ['count'=>$count],
				where: $where
			);
			
			return str_pad( $count, $pad, '0', STR_PAD_LEFT );
		}
		
		$count = 1;
		
		$item = new static();
		$item->entity = $entity->getNumberSeriesEntityType();
		if($shop) {
			$item->shop_key = $shop->getKey();
		}
		$item->count = $count;
		$item->save();
		
		return str_pad( $count, $pad, '0', STR_PAD_LEFT );
	}
	
}