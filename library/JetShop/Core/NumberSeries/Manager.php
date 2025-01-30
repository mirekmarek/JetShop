<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\EShopEntity_HasNumberSeries_Interface;

abstract class Core_NumberSeries_Manager extends Application_Module {
	
	abstract public function generateNumber( EShopEntity_HasNumberSeries_Interface $entity ) : string;
	
}