<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


interface Core_EShopEntity_HasActivation_Interface {
	
	public function isActive() : bool;
	public function activate() : void;
	public function deactivate() : void;

}