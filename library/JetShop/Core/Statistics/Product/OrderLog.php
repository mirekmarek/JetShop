<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Locale;
use JetApplication\EShopEntity_AccountingDocument_Item;
use JetApplication\Order;
use JetApplication\Product_EShopData;

#[DataModel_Definition(
	name: 'product_order_log',
	database_table_name: 'prouduct_order_log',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_Statistics_Product_OrderLog extends DataModel {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $eshop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
	)]
	protected ?Locale $locale = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $kind_of_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		is_key: true,
	)]
	protected float $number_of_units = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $year = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $month = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $day = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $hour = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $minute = 0;
	
	public static function rec( Order $order ) : void
	{
		foreach($order->getItems() as $order_item) {
			if(
				$order_item->getType()!=EShopEntity_AccountingDocument_Item::ITEM_TYPE_PRODUCT &&
				$order_item->getType()!=EShopEntity_AccountingDocument_Item::ITEM_TYPE_VIRTUAL_PRODUCT
			) {
				return;
			}
			
			$now = $order->getDatePurchased();
			
			
			$product = Product_EShopData::get( $order_item->getItemId(), $order->getEshop() );
			if($product) {
				$i = new static();
				$i->eshop_code = $order->getEshopCode();
				$i->locale = $order->getLocale();
				$i->product_id = $product->getId();
				$i->kind_of_product_id = $product->getKindId();
				$i->number_of_units = $order_item->getNumberOfUnits();
				$i->date_time = $now;
				
				$i->year = $now->format('Y');
				$i->month = $now->format('m');
				$i->day = $now->format('d');
				$i->hour = $now->format('H');
				$i->minute = $now->format('i');
				
				$i->save();
			}
			
			
			foreach($order_item->getSetItems() as $set_item) {
				$product = Product_EShopData::get( $set_item->getItemId(), $order->getEshop() );
				if($product) {
					$i = new static();
					$i->eshop_code = $order->getEshopCode();
					$i->locale = $order->getLocale();
					$i->product_id = $product->getId();
					$i->kind_of_product_id = $product->getKindId();
					$i->number_of_units = $order_item->getNumberOfUnits()*$set_item->getNumberOfUnits();
					$i->date_time = $now;
					
					$i->year = $now->format('Y');
					$i->month = $now->format('m');
					$i->day = $now->format('d');
					$i->hour = $now->format('H');
					$i->minute = $now->format('i');
					
					$i->save();
				}
			}
		}
		
	}
}