<?php

namespace JetShop;

use Jet\Form;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;

abstract class Core_Timer_Action {
	protected EShop $eshop;
	
	abstract public function getKey() : string;
	abstract public function getTitle() : string;
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function setEshop( EShop $eshop ): void
	{
		$this->eshop = $eshop;
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
	
	abstract public function perform( Entity_WithEShopData $entity, mixed $action_context ) : bool;
}