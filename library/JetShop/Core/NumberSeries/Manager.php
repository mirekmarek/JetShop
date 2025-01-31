<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\EShopEntity_HasNumberSeries_Interface;

abstract class Core_NumberSeries_Manager extends Application_Module {
	
	abstract public function generateNumber( EShopEntity_HasNumberSeries_Interface $entity ) : string;
	
}