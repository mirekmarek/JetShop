<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\Email\Layouts;

use JetApplication\Admin_EntityManager_Controller;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'E-mail layout';
	}
	
}