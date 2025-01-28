<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Classes;

use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Delivery class';
	}
	
}