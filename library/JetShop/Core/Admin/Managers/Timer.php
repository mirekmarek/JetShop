<?php
namespace JetShop;

use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;


interface Core_Admin_Managers_Timer
{
	public function renderIntegration() : string;
	public function renderIcon( Entity_WithShopData $entity, Shops_Shop $shop ) : string;

}