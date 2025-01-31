<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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