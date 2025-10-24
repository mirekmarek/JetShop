<?php

namespace Twisto;


interface BaseAddress
{
    const TYPE_FULL = 1;
    const TYPE_SHORT = 2;

    public function serialize() : array;
    static function deserialize( array $data) : static;
}
