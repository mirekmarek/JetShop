<?php
namespace JetApplicationModule\Admin\Content\Article\Authors;

use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Article author';
	}
	
}