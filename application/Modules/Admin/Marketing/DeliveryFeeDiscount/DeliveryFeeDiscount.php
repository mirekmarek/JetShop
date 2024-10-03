<?php
/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Marketing\DeliveryFeeDiscount;

use Jet\Form;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_DeliveryFeeDiscount;

class DeliveryFeeDiscount extends Marketing_DeliveryFeeDiscount implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;

	public function hasImages(): bool
	{
		return false;
	}
	
	public function setupForm( Form $form ): void
	{
	}
	
	public function getEditURL(): string
	{
		return Main::getEditUrl( $this->id );
	}
}