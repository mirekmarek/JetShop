<?php
namespace Twisto;

class ItemReturn
{
    public string $product_id;
    public int $quantity;
	
	public function __construct(string $product_id, int $quantity)
    {
        $this->product_id = $product_id;
        $this->quantity = $quantity;
    }

    public function serialize() : array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity
        ];
    }
}