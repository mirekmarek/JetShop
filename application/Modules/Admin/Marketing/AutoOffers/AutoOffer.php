<?php
namespace JetApplicationModule\Admin\Marketing\AutoOffers;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_AutoOffer as Application_AutoOffer;

#[DataModel_Definition]
class AutoOffer extends Application_AutoOffer implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'main',
			image_title:  Tr::_('Main image'),
		);
		$this->defineImage(
			image_class:  'pictogram',
			image_title:  Tr::_('Pictogram image'),
		);
	}
	
}