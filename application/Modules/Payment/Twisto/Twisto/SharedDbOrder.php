<?php
namespace Twisto;

use DateTime;

class SharedDbOrder
{
    public string $order_id;
    public DateTime $date_created;
    public float $total_price_vat;

    public function __construct(string $order_id, DateTime $date_created, float $total_price_vat)
    {
        $this->order_id = $order_id;
        $this->date_created = $date_created;
        $this->total_price_vat = $total_price_vat;
    }

    public function serialize() : array
    {
        return array(
            'order_id' => $this->order_id,
            'date_created' => $this->date_created->format('c'), // ISO 8601
            'approx_total_price' => ($this->total_price_vat <= 500 ? 1 : 2)
        );
    }
}
