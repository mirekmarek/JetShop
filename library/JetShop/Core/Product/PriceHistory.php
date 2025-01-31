<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Basic;
use JetApplication\Pricelist;
use JetApplication\Product_Price;

#[DataModel_Definition(
	name: 'products_price_history',
	database_table_name: 'products_price_history'
)]
abstract class Core_Product_PriceHistory extends EShopEntity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $pricelist_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $price = 0.0;
	
	public function getProductId(): int
	{
		return $this->product_id;
	}

	public function setProductId( int $product_id ): void
	{
		$this->product_id = $product_id;
	}
	
	public function getDateTime(): Data_DateTime
	{
		return $this->date_time;
	}
	

	public function setDateTime( Data_DateTime $date_time ): void
	{
		$this->date_time = $date_time;
	}
	
	public function getPrice(): float
	{
		return $this->price;
	}

	public function setPrice( float $price ): void
	{
		$this->price = $price;
	}
	
	public static function newRecord( Product_Price $price ) : static
	{
		$rec = new static();
		
		$rec->pricelist_code = $price->getPricelistCode();
		$rec->product_id = $price->getEntityId();
		$rec->date_time = Data_DateTime::now();
		$rec->price = $price->getPrice();
		
		$rec->save();
		
		return $rec;
	}
	
	/**
	 * @return static[]
	 */
	public static function get( Pricelist $pricelist, int $product_id ) : array
	{
		$where = [];
		$where['product_id'] = $product_id;
		$where[] = 'AND';
		$where['pricelist_code'] = $pricelist->getCode();
		
		$_history = static::fetchInstances( $where );
		$_history->getQuery()->setOrderBy('-id');
		
		$history = [];
		foreach($_history as $hi) {
			$history[] = $hi;
		}
		
		return $history;
		
	}
	
}