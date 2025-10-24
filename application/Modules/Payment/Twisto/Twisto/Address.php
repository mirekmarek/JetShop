<?php

namespace Twisto;


class Address implements BaseAddress
{
    public string $name;
    public string $street;
    public string $city;
    public string $zipcode;
    public string $phone_number;
    public string $country;

    public function __construct(string $name, string $street, string $city, string $zipcode, string $country, string $phone_number)
    {
        $this->name = $name;
        $this->street = $street;
        $this->city = $city;
        $this->zipcode = $zipcode;
        $this->country = $country;
        $this->phone_number = $phone_number;
    }

    public function serialize() : array
    {
        return [
            'name' => $this->name,
            'street' => $this->street,
            'city' => $this->city,
            'zipcode' => $this->zipcode,
            'country' => $this->country,
            'phone_number' => $this->phone_number
        ];
    }

    public static function deserialize( array $data ) : static
    {
        return new self($data['name'], $data['street'], $data['city'], $data['zipcode'], $data['country'], $data['phone_number']);
   }
}