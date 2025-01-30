<?php
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Basic;
use JetApplication\Pricelists;
use JetApplication\Pricelist;

#[DataModel_Definition]
abstract class Core_EShopEntity_Price extends EShopEntity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $pricelist_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $entity_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		is_key: true
	)]
	protected float $vat_rate = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		is_key: true
	)]
	protected float $price = 0.0;
	
	protected bool $price_updated = false;
	
	protected static array $loaded = [];
	
	public static function get( Pricelist $pricelist, int $entity_id ) : static
	{
		
		if(!isset(static::$loaded[static::class][$pricelist->getCode()][$entity_id])) {
			
			$price = static::load([
				'pricelist_code' => $pricelist->getCode(),
				'AND',
				'entity_id' => $entity_id
			]);
			
			if(!$price) {
				$price = new static();
				$price->setPricelistCode( $pricelist->getCode() );
				$price->setEntityId( $entity_id );
				$price->setVatRate( $pricelist->getDefaultVatRate() );
			}
			
			
			$custoun_discount_mtp = $pricelist->getCustomDiscountMtp( static::class );
			if($custoun_discount_mtp) {
				$price->setPrice( $price->getPrice()*$custoun_discount_mtp );
			}
			
			static::$loaded[static::class][$pricelist->getCode()][$entity_id] = $price;
		}
		
		return static::$loaded[static::class][$pricelist->getCode()][$entity_id];
	}
	
	/**
	 * @param Pricelist $pricelist
	 * @param array $entity_ids
	 *
	 * @return static[]
	 */
	public static function prefetch( Pricelist $pricelist, array $entity_ids ) : array
	{
		if(!$entity_ids) {
			return [];
		}
		
		$prices = static::fetch([''=>[
			'pricelist_code' => $pricelist->getCode(),
			'AND',
			'entity_id' => $entity_ids
		]]);
		
		$custoun_discount_mtp = $pricelist->getCustomDiscountMtp( static::class );
		
		if($custoun_discount_mtp) {
			foreach( $prices as $price ) {
				$price->setPrice( $price->getPrice()*$custoun_discount_mtp );
				
				static::$loaded[ static::class ][$pricelist->getCode()][$price->getEntityId()] = $price;
			}
		} else {
			foreach( $prices as $price ) {
				static::$loaded[ static::class ][$pricelist->getCode()][$price->getEntityId()] = $price;
			}
			
		}
		
		return $prices;
	}
	
	
	/**
	 * @param Pricelist $pricelist
	 *
	 * @return static[]
	 */
	public static function prefetchAll( Pricelist $pricelist ) : array
	{
		$prices = static::fetch([''=>[
			'pricelist_code' => $pricelist->getCode()
		]]);
		
		$custoun_discount_mtp = $pricelist->getCustomDiscountMtp( static::class );
		
		if($custoun_discount_mtp) {
			foreach( $prices as $price ) {
				$price->setPrice( $price->getPrice()*$custoun_discount_mtp );
				
				static::$loaded[ static::class ][$pricelist->getCode()][$price->getEntityId()] = $price;
			}
		} else {
			foreach( $prices as $price ) {
				static::$loaded[ static::class ][$pricelist->getCode()][$price->getEntityId()] = $price;
			}
			
		}
		
		return $prices;
	}
	
	
	public static function getPriceMap( Pricelist $pricelist, array $entity_ids ) : array
	{
		$data = static::dataFetchAll(
			select:['entity_id', 'price'],
			where: [
				'pricelist_code' => $pricelist->getCode(),
				'AND',
				'entity_id' => $entity_ids
			],
			raw_mode: true
		);
		
		$prices = [];
		
		$custoun_discount_mtp = $pricelist->getCustomDiscountMtp( static::class );
		
		foreach($data as $p) {
			$price = (float)$p['price'];
			
			if($custoun_discount_mtp) {
				$price = $price*$custoun_discount_mtp;
			}
			
			$prices[] = $price;
		}
		
		return $prices;
	}
	
	public static function orderAsc( Pricelist $pricelist, array $entity_ids ) : array
	{
		return static::dataFetchCol(
			select: ['entity_id'],
			where: [
				'pricelist_code' => $pricelist->getCode(),
				'AND',
				'entity_id' => $entity_ids
			],
			order_by: ['price'],
			raw_mode: true);
		
	}
	
	public static function orderDesc( Pricelist $pricelist, array $entity_ids ) : array
	{
		return static::dataFetchCol(
			select: ['entity_id'],
			where: [
				'pricelist_code' => $pricelist->getCode(),
				'AND',
				'entity_id' => $entity_ids
			],
			order_by: ['-price'],
			raw_mode: true);
		
	}
	
	public static function filterMinMax(
		Pricelist      $pricelist,
		null|int|float $min_price = null,
		null|int|float $max_price = null,
		?array         $entity_ids = null
	) : array
	{
		
		if($pricelist->getCustomDiscountPrc( static::class )) {
			$mtp = 100 - $pricelist->getCustomDiscountPrc( static::class );
			
			if($min_price!==null) {
				$min_price = ($min_price / $mtp)*100;
			}
			if($max_price!==null) {
				$max_price = ($max_price / $mtp)*100;
			}
		}
		
		$where = [
			'pricelist_code' => $pricelist->getCode()
		];
		
		if($entity_ids!==null) {
			$where[] = 'AND';
			$where['entity_id'] = $entity_ids;
		}
		
		if($min_price!==null) {
			$where[] = 'AND';
			$where[] = ['price >='=>$min_price];
		}
		
		if($max_price!==null) {
			$where[] = 'AND';
			$where[] = ['price <='=>$max_price];
		}
		
		$data = static::dataFetchAll(
			select: ['entity_id'],
			where: $where,
			raw_mode: true
		);
		
		$filter_result = [];
		foreach($data as $d) {
			$filter_result[] = (int)$d['entity_id'];
		}
		
		return $filter_result;
	}
	
	public static function filterHasDiscount(
		Pricelist $pricelist,
		bool      $has_discount,
		?array    $entity_ids = null
	) : array
	{
		
		$where = [
			'pricelist_code' => $pricelist->getCode()
		];
		
		if($entity_ids!==null) {
			$where[] = 'AND';
			$where['entity_id'] = $entity_ids;
		}
		
		$where[] = 'AND';
		if($has_discount) {
			$where[] = ['discount_percentage >'=>0];
		} else {
			$where[] = ['discount_percentage'=>0];
		}
		
		
		$data = static::dataFetchAll(
			select: ['entity_id'],
			where: $where,
			raw_mode: true
		);
		
		$filter_result = [];
		foreach($data as $d) {
			$filter_result[] = (int)$d['entity_id'];
		}
		
		return $filter_result;
	}
	
	
	
	public function getEntityId(): int
	{
		return $this->entity_id;
	}
	
	public function setEntityId( int $entity_id ): void
	{
		$this->entity_id = $entity_id;
	}
	
	
	public function getPricelistCode(): string
	{
		return $this->pricelist_code;
	}
	
	public function setPricelistCode( string $pricelist_code ): void
	{
		$this->pricelist_code = $pricelist_code;
	}
	
	public function getPricelist() : Pricelist
	{
		return Pricelists::get( $this->pricelist_code );
	}
	
	
	
	public function getVatRate(): float
	{
		return $this->vat_rate;
	}
	
	public function setVatRate( float $vat_rate ): void
	{
		$this->vat_rate = $vat_rate;
	}
	
	public function setPrice( float $price ): void
	{
		
		if($this->price==$price) {
			return;
		}
		
		$this->price = $price;
		$this->price_updated = true;
	}
	
	
	public function getPrice(): float
	{
		return $this->price;
	}
	
	public function setPriceWithoutVAT( float $price_without_vat ) : void
	{
		$pricelist = $this->getPricelist();
		if(
			$pricelist->getPricesAreWithoutVat() ||
			$this->vat_rate==0
		) {
			$this->price = $price_without_vat;
			return;
		}
		
		$mtp = 1 + ( $this->vat_rate / 100 );
		
		$this->price = $pricelist->round_WithVAT( $price_without_vat*$mtp );
	}
	
	public function getPrice_WithoutVAT() : float
	{
		$pricelist = $this->getPricelist();
		if(
			$pricelist->getPricesAreWithoutVat() ||
			$this->vat_rate==0
		) {
			return $this->price;
		}
		
		$dvd = 1 + ( $this->vat_rate / 100 );
		
		return $pricelist->round_WithoutVAT( $this->price / $dvd );
	}
	
	public function getPrice_WithVAT() : float
	{
		$pricelist = $this->getPricelist();
		if(
			!$pricelist->getPricesAreWithoutVat() ||
			$this->vat_rate==0
		) {
			return $this->price;
		}
		
		$mtp = 1 + ( $this->vat_rate / 100 );
		
		return $pricelist->round_WithVAT( $this->price * $mtp );
	}
	
	public function getPrice_VAT() : float
	{
		$pricelist = $this->getPricelist();
		if( $this->vat_rate==0 ) {
			return 0;
		}
		
		
		if($pricelist->getPricesAreWithoutVat()) {
			$price_without_vat = $this->price;
		} else {
			$dvd = 1 + ( $this->vat_rate / 100 );
			
			$price_without_vat = $this->price / $dvd;
		}
		
		$mtp = ( $this->vat_rate / 100 );
		
		return $pricelist->round_VAT( $price_without_vat * $mtp );
	}
	
}