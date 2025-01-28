<?php
namespace JetShop;

interface Core_Entity_HasActivation_Interface {
	
	public function isActive() : bool;
	public function activate() : void;
	public function deactivate() : void;

}