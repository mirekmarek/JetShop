<?php
namespace JetShop;

interface Core_EShopEntity_HasActivation_Interface {
	
	public function isActive() : bool;
	public function activate() : void;
	public function deactivate() : void;

}