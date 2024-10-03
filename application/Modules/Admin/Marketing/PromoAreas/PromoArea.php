<?php
/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Marketing\PromoAreas;

use Jet\Form;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_PromoArea;

class PromoArea extends Marketing_PromoArea implements Admin_Entity_Marketing_Interface
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