<?php
namespace JetShop;

use JetApplication\Entity_HasTimer_Interface;


interface Core_Admin_Managers_Timer
{
	public function renderIntegration() : string;
	
	public function renderEntityEdit( Entity_HasTimer_Interface $entity, bool $editable ) : string;

}