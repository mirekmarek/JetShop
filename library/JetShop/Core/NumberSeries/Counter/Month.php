<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\NumberSeries_Counter;
use JetApplication\NumberSeries_Entity_Interface;

#[DataModel_Definition(
	name: 'number_series_counter_month',
	database_table_name: 'number_series_counter_month',
)]
abstract class Core_NumberSeries_Counter_Month extends NumberSeries_Counter
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_id: true,
	)]
	protected string $month = '';
	
	public static function generate( NumberSeries_Entity_Interface $entity, int $pad ) : string
	{
		$where = [
			'entity' => $entity->getNumberSeriesEntityType()
		];
		if( ($shop=$entity->getNumberSeriesEntityShop()) ) {
			$where[] = 'AND';
			$where['shop_key'] = $shop->getKey();
		}
		
		$where[] = 'AND';
		$where['month'] = $entity->getNumberSeriesEntityData()->format('Y-m');
		
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
			
			return $entity->getNumberSeriesEntityData()->format('Ym').str_pad( $count, $pad, '0', STR_PAD_LEFT );
		}
		
		$count = 1;
		
		$item = new static();
		$item->entity = $entity->getNumberSeriesEntityType();
		if($shop) {
			$item->shop_key = $shop->getKey();
		}
		$item->month = $entity->getNumberSeriesEntityData()->format('Y-m');
		$item->count = $count;
		$item->save();
		
		return $entity->getNumberSeriesEntityData()->format('Ym').str_pad( $count, $pad, '0', STR_PAD_LEFT );
	}
	
	
}