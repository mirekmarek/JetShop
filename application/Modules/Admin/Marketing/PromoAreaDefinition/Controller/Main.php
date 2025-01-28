<?php
namespace JetApplicationModule\Admin\Marketing\PromoAreaDefinition;

use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Promo area definition';
	}
	
}