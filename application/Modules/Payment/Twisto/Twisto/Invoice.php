<?php
namespace Twisto;


use DateTime;

class Invoice
{
    private Twisto $twisto;
    public ?string $invoice_id = null;
    public ?string $eshop_invoice_id = null;
    public ?string $customer_email = null;
    public ?BaseAddress $billing_address = null;
    public ?BaseAddress $delivery_address = null;
    public ?DateTime $date_created = null;
    public ?DateTime $date_returned = null;
    public ?DateTime $date_cancelled = null;
    public ?DateTime $date_activated = null;
    public ?DateTime $date_paid = null;
    public ?string $pdf_url = null;
    public ?float $total_price_vat = null;
	
	/**
	 * @var null|array<Item>
	 */
    public ?array $items = null;

    public function __construct(Twisto $twisto, ?string $invoice_id)
    {
        $this->twisto = $twisto;
        $this->invoice_id = $invoice_id;
    }

    public function get() : void
    {
        $data = $this->twisto->requestJson('GET', 'invoice/' . urlencode($this->invoice_id) . '/');
        $this->deserialize($data);
    }

    public function cancel() : void
    {
        $data = $this->twisto->requestJson('POST', 'invoice/' . urlencode($this->invoice_id) . '/cancel/');
        $this->deserialize($data);
    }

    public function activate() : void
    {
        $data = $this->twisto->requestJson('POST', 'invoice/' . urlencode($this->invoice_id) . '/activate/');
        $this->deserialize($data);
    }

    public function save() : void
    {
        $data = $this->twisto->requestJson('POST', 'invoice/' . urlencode($this->invoice_id) . '/edit/', $this->serialize());
        $this->deserialize($data);
    }

    public static function create(Twisto $twisto, string $transaction_id, ?string $eshop_invoice_id = null) : Invoice
    {
        $data = array(
            'transaction_id' => $transaction_id
        );

        if ($eshop_invoice_id !== null) {
            $data['eshop_invoice_id'] = $eshop_invoice_id;
        }

        $data = $twisto->requestJson('POST', 'invoice/', $data);
        $invoice = new Invoice($twisto, null);
        $invoice->deserialize($data);
        return $invoice;
    }

    /**
     * @param array<ItemReturn> $items
     * @param null|array<ItemDiscountReturn> $discounts
     */
    public function returnItems( array $items, ?array $discounts = null) : void
    {
        $data = array(
            'items' => array_map(function(ItemReturn $item) {
                return $item->serialize();
            }, $items)
        );

        if ($discounts !== null) {
            $data['discounts'] = array_map(function(ItemDiscountReturn $item) {
                return $item->serialize();
            }, $discounts);
        }

        $data = $this->twisto->requestJson('POST', 'invoice/' . urlencode($this->invoice_id) . '/return/', $data);
        $this->deserialize($data);
    }

    public function returnAll() : void
    {
        $data = $this->twisto->requestJson('POST', 'invoice/' . urlencode($this->invoice_id) . '/return/all/');
        $this->deserialize($data);
    }

    private function deserialize( array $data) : void
    {
        $this->invoice_id = $data['invoice_id'];
        $this->eshop_invoice_id = $data['eshop_invoice_id'];
        $this->customer_email = $data['customer_email'];

        if ($data['billing_address']['type'] == BaseAddress::TYPE_SHORT) {
            $this->billing_address = ShortAddress::deserialize($data['billing_address']);
        }
        else {
            $this->billing_address = Address::deserialize($data['billing_address']);
        }

        if ($data['delivery_address']['type'] == BaseAddress::TYPE_SHORT) {
            $this->delivery_address = ShortAddress::deserialize($data['delivery_address']);
        }
        else {
            $this->delivery_address = Address::deserialize($data['delivery_address']);
        }

        if ($data['date_created'])
            $this->date_created = new DateTime($data['date_created']);

        if ($data['date_returned'])
            $this->date_returned = new DateTime($data['date_returned']);

        if ($data['date_cancelled'])
            $this->date_cancelled = new DateTime($data['date_cancelled']);

        if ($data['date_activated'])
            $this->date_activated = new DateTime($data['date_activated']);

        if ($data['date_paid'])
            $this->date_paid = new DateTime($data['date_paid']);

        $this->pdf_url = $data['pdf_url'];
        $this->total_price_vat = (float)$data['total_price_vat'];

        $this->items = array_map(function($item) {
            return Item::deserialize($item);
        }, $data['items']);
    }

    private function serialize() : array
    {
        $data = [];

        if ($this->eshop_invoice_id !== null)
            $data['eshop_invoice_id'] = $this->eshop_invoice_id;

        if ($this->items !== null) {
            $data['items'] = array_map(function(Item $item) {
                return $item->serialize();
            }, $this->items);
        }

        return $data;
    }
}