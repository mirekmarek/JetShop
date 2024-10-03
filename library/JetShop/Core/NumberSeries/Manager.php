<?php
namespace JetShop;

use Jet\Application_Module;
use JetApplication\NumberSeries_Entity_Interface;

abstract class Core_NumberSeries_Manager extends Application_Module {
	
	abstract public function generateNumber( NumberSeries_Entity_Interface $entity ) : string;
	
}