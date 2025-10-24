<?php
namespace Twisto;

class ItemDiscountReturn
{

    public string $product_id;
    public float $price_vat;
	
	public function __construct( string $product_id, float $price_vat)
    {
        $this->product_id = $product_id;
        $this->price_vat = $price_vat;
    }

    public function serialize() : array
    {
        return [
            'product_id' => $this->product_id,
            'price_vat' => $this->price_vat
        ];
    }
}