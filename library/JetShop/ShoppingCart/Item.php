<?php
namespace JetShop;

class ShoppingCart_Item extends Core_ShoppingCart_Item
{
	public function checkQuantity( int $quantity, bool $generate_error_message=false ) : bool
	{
		return true;
	}

	public function generateDeliveryTermInfo() : string
	{
		return '';
	}

}