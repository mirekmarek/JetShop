<?php
namespace JetApplicationModule\Admin\Content\Article\KindOf;

use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Kind of article';
	}
	
}