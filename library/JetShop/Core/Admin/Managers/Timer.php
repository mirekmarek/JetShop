<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_HasTimer_Interface;


interface Core_Admin_Managers_Timer
{
	public function renderIntegration() : string;
	
	public function renderEntityEdit( EShopEntity_HasTimer_Interface $entity, bool $editable ) : string;

}