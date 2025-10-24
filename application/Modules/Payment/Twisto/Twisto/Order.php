<?php

namespace Twisto;


use DateTime;

class Order
{
    public DateTime $date_created;
    public BaseAddress $billing_address;
    public BaseAddress $delivery_address;
    public float $total_price_vat;
	
	/**
	 * @var array<Item>
	 */
    public array $items;

    public function __construct( DateTime $date_created, BaseAddress $billing_address, BaseAddress $delivery_address, float $total_price_vat, array $items)
    {
        $this->date_created = $date_created;
        $this->billing_address = $billing_address;
        $this->delivery_address = $delivery_address;
        $this->total_price_vat = $total_price_vat;
        $this->items = $items;
    }

    public function serialize() : array
    {
        return array(
            'date_created' => $this->date_created->format('c'), // ISO 8601
            'billing_address' => $this->billing_address->serialize(),
            'delivery_address' => $this->delivery_address->serialize(),
            'total_price_vat' => $this->total_price_vat,
            'items' => array_map(function (Item $order) {
                return $order->serialize();
            }, $this->items)
        );
    }
}