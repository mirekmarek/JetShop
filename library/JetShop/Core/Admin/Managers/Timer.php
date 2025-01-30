<?php
namespace JetShop;

use JetApplication\EShopEntity_HasTimer_Interface;


interface Core_Admin_Managers_Timer
{
	public function renderIntegration() : string;
	
	public function renderEntityEdit( EShopEntity_HasTimer_Interface $entity, bool $editable ) : string;

}