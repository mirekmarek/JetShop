<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\SetProductPrices;


class ProductPriceList_Item {

	protected string $identifier;
	
	protected int $id;
	protected string $ean;
	protected string $internal_code;
	
	protected float $vat_rate;
	protected string $name;
	protected float $price;
	
	protected ?float $new_price = null;
	
	public function __construct( array $d, string $identifier )
	{
		$this->identifier = $identifier;
		
		$this->id = (int)$d['id'];
		$this->ean = $d['ean'];
		$this->internal_code = $d['internal_code'];
		$this->name = $d['name'];
		
		$this->vat_rate = (float)$d['vat_rate'];
		$this->price = (float)$d['price'];
	}
	
	public function getProductIdentification() : mixed
	{
		return $this->{$this->identifier};
	}
	

	public function getId(): int
	{
		return $this->id;
	}
	
	public function getEan(): string
	{
		return $this->ean;
	}
	
	public function getInternalCode(): string
	{
		return $this->internal_code;
	}
	
	public function getVatRate(): float
	{
		return $this->vat_rate;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getPrice(): float
	{
		return $this->price;
	}
	
	public function getNewPrice(): ?float
	{
		return $this->new_price;
	}
	
	public function setNewPrice( ?float $new_price ): void
	{
		$this->new_price = $new_price;
	}
	
	
	
}