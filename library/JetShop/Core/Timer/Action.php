<?php

namespace JetShop;

use Jet\Form;
use JetApplication\EShopEntity_Basic;


abstract class Core_Timer_Action {
	
	abstract public function getAction();
	abstract public function getTitle() : string;
	
	
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

	
	abstract public function perform( EShopEntity_Basic $entity, mixed $action_context ) : bool;
}