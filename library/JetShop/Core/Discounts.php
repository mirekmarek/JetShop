<?php
namespace JetShop;

use JetApplication\Discounts_Manager;
use JetApplication\Managers_General;

class Core_Discounts
{
	public static function Manager() : Discounts_Manager
	{
		return Managers_General::Discounts();
	}
	
}
