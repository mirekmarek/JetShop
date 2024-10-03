<?php

namespace JetShop;

use Jet\Form;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;

abstract class Core_Timer_Action {
	protected Shops_Shop $shop;
	
	abstract public function getKey() : string;
	abstract public function getTitle() : string;
	
	public function getShop(): Shops_Shop
	{
		return $this->shop;
	}
	
	public function setShop( Shops_Shop $shop ): void
	{
		$this->shop = $shop;
	}
	
	public function updateForm( Form $form ) : void
	{
	}
	
	public function catchActionContextValue( Form $form ) : mixed
	{
		return null;
	}
	
	public function formatActionContextValue( mixed $action_context ) : string
	{
		return '';
	}
	
	abstract public function perform( Entity_WithShopData $entity, mixed $action_context ) : bool;
}