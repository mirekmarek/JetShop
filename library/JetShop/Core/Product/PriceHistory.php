<?php
namespace JetShop;


use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Product_ShopData;

#[DataModel_Definition(
	name: 'products_price_history',
	database_table_name: 'products_price_history'
)]
abstract class Core_Product_PriceHistory extends Entity_WithShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	
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
	
	public static function newRecord( Product_ShopData $product ) : static
	{
		$rec = new static();
		$rec->setShop( $product->getShop() );
		$rec->product_id = $product->getId();
		$rec->date_time = Data_DateTime::now();
		$rec->price = $product->getPrice();
		
		$rec->save();
		
		return $rec;
	}
	
	
}