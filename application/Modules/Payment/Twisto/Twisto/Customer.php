<?php
namespace Twisto;

class Customer
{
    public string $email;
    public ?string $name;
    public ?string $facebook_id;
    public ?string $company_id;
    public ?string $vat_id;

    public function __construct(string $email, ?string $name=null, ?string $facebook_id=null, ?string $company_id=null, ?string $vat_id=null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->facebook_id = $facebook_id;
        $this->company_id = $company_id;
        $this->vat_id = $vat_id;
    }

    public function serialize() : array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'facebook_id' => $this->facebook_id,
            'company_id' => $this->company_id,
            'vat_id' => $this->vat_id
        ];
    }
}