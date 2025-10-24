<?php

namespace Twisto;


interface BaseAddress
{
	public const TYPE_FULL = 1;
	public const TYPE_SHORT = 2;

    public function serialize() : array;
	public static function deserialize( array $data) : static;
}
