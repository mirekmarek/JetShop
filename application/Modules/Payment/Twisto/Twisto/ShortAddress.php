<?php
namespace Twisto;


class ShortAddress implements BaseAddress
{
    public string $name;
    public string $phone_number;
    public string $country;

    public function __construct(string $name, string $country, string $phone_number)
    {
        $this->name = $name;
        $this->country = $country;
        $this->phone_number = $phone_number;
    }

    public function serialize() : array
    {
        return [
            'type' => self::TYPE_SHORT,
            'name' => $this->name,
            'country' => $this->country,
            'phone_number' => $this->phone_number
        ];
    }

    public static function deserialize($data) : static
    {
        return new static($data['name'], $data['country'], $data['phone_number']);
    }
}