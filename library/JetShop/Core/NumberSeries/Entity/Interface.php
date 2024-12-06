<?php
namespace JetShop;


use Jet\Data_DateTime;
use JetApplication\EShop;

interface Core_NumberSeries_Entity_Interface
{
	public function getNumberSeriesEntityType() : string;
	
	public function getNumberSeriesEntityId() : int;
	
	public function getNumberSeriesEntityData() : ?Data_DateTime;
	
	public function getNumberSeriesEntityShop() : ?EShop;
	
	public function generateNumber() : void;
	
}