<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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
	
	protected ?Pricelist $pricelist=null;
	
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
	protected bool $price_recalculated = false;
	
	/**
	 * @var static[][][]
	 */
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
			} else {
				$price->recalculatePrice();
			}
			$price->pricelist = $pricelist;
			
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
		
		$load_ids = [];
		foreach( $entity_ids as $entity_id ) {
			if(!isset(static::$loaded[ static::class ][$pricelist->getCode()][$entity_id])) {
				$load_ids[] = $entity_id;
			}
		}
		
		if($load_ids) {
			$prices = static::fetch([''=>[
				'pricelist_code' => $pricelist->getCode(),
				'AND',
				'entity_id' => $entity_ids
			]]);
			
			foreach( $prices as $price ) {
				$price->pricelist = $pricelist;
				$price->recalculatePrice();
				static::$loaded[ static::class ][$pricelist->getCode()][$price->getEntityId()] = $price;
			}
		}
		
		$prices = [];
		
		foreach($entity_ids as $entity_id) {
			if(isset(static::$loaded[ static::class ][$pricelist->getCode()][$entity_id])) {
				$prices[$entity_id] = static::$loaded[ static::class ][$pricelist->getCode()][$entity_id];
			}
		}
		
		return $prices;
	}
	
	public static function orderAsc( Pricelist $pricelist, array $entity_ids ) : array
	{
		$prices = static::prefetch( $pricelist, $entity_ids );
		
		$map = [];
		
		foreach($prices as $price) {
			$map[$price->getEntityId()] = $price->getPrice();
		}
		
		asort( $map, SORT_NUMERIC );
		return array_keys( $map );
	}
	
	public static function orderDesc( Pricelist $pricelist, array $entity_ids ) : array
	{
		$prices = static::prefetch( $pricelist, $entity_ids );
		
		$map = [];
		
		foreach($prices as $price) {
			$map[$price->getEntityId()] = $price->getPrice();
		}
		
		arsort( $map, SORT_NUMERIC );
		return array_keys( $map );
	}
	
	
	
	public static function filterMinMax(
		Pricelist      $pricelist,
		array         $entity_ids,
		null|int|float $min_price = null,
		null|int|float $max_price = null,
	) : array
	{
		$prices = static::prefetch( $pricelist, $entity_ids );
		
		$filter_result = [];
		foreach($prices as $price) {
			
			if(
				$min_price!==null &&
				$price->getPrice() < $min_price
			) {
				continue;
			}
			
			if(
				$max_price!==null &&
				$price->getPrice() > $max_price
			) {
				continue;
			}
			
			$filter_result[] = $price->getEntityId();
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
		if(!$this->pricelist) {
			$this->pricelist = Pricelists::get( $this->pricelist_code );
		}
		return $this->pricelist;
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
	

	protected function recalculatePrice() : void
	{
		if($this->price_recalculated) {
			return;
		}
		
		//come custom price logic ...

		$this->price_recalculated = true;
	}
}