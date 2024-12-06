<?php
namespace JetShop;

use JetApplication\Entity_WithEShopData;


interface Core_Admin_Managers_Timer
{
	public function renderIntegration() : string;
	
	public function renderEntityEdit( Entity_WithEShopData $entity, bool $editable ) : string;

}