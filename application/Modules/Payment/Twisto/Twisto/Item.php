<?php
namespace Twisto;

class Item
{
    public const TYPE_DEFAULT = 0;
	public const TYPE_SHIPMENT = 1;
	public const TYPE_PAYMENT = 2;
	public const TYPE_DISCOUNT = 4;
	public const TYPE_ROUND = 32;
	
    public ?int $type = null;
    public ?string $name = null;
    public ?string $product_id = null;
    public ?int $quantity = null;
    public ?float $price_vat = null;
    public ?float $vat = null;
    public ?string $ean_code = null;
    public ?string $isbn_code = null;
    public ?string $issn_code = null;
    public ?string $heureka_category = null;
	
	public function __construct( int $type, string $name, string $product_id, int $quantity, float $price_vat, float $vat, ?string $ean_code=null, ?string $isbn_code=null, ?string $issn_code=null, ?string $heureka_category=null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->price_vat = $price_vat;
        $this->vat = $vat;
        $this->ean_code = $ean_code;
        $this->isbn_code = $isbn_code;
        $this->issn_code = $issn_code;
        $this->heureka_category = $heureka_category;
    }

    public function serialize() : array
    {
        return array(
            'type' => $this->type,
            'name' => $this->name,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price_vat' => $this->price_vat,
            'vat' => $this->vat,
            'ean_code' => $this->ean_code,
            'isbn_code' => $this->isbn_code,
            'issn_code' => $this->issn_code,
            'heureka_category' => $this->heureka_category
        );
    }

    public static function deserialize( array $data) : static
    {
        return new static($data['type'], $data['name'], $data['product_id'], $data['quantity'], $data['price_vat'], $data['vat'], $data['ean_code'], $data['isbn_code'], $data['issn_code'], $data['heureka_category']);
    }
}