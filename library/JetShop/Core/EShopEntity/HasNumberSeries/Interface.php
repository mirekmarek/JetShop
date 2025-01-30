<?php
namespace JetShop;


use Jet\Data_DateTime;
use JetApplication\EShop;

interface Core_EShopEntity_HasNumberSeries_Interface
{
	public static function getNumberSeriesEntityType() : string;
	
	public static function getNumberSeriesEntityTitle() : string;
	
	public static function getNumberSeriesEntityIsPerShop() : bool;
	
	public function getNumberSeriesEntityId() : int;
	
	public function getNumberSeriesEntityData() : ?Data_DateTime;
	
	public function getNumberSeriesEntityShop() : ?EShop;
	
	public function generateNumber() : void;
	
}