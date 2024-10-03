<?php
namespace JetShop;


use JetApplication\Shops_Shop;

interface Core_Admin_ControlCentre_Module_Interface {
	
	public function getControlCentreGroup() : string;

	public function getControlCentreTitle() : string;
	
	public function getControlCentreTitleTranslated() : string;
	
	public function getControlCentreIcon() : string;
	
	public function getControlCentrePriority() : int;
	
	public function getControlCentrePerShopMode() : bool;
	
	public function handleControlCentre( ?Shops_Shop $shop=null ) : string;

}