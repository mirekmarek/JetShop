<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Entity_Common;
use JetApplication\Admin_Entity_Common_Interface;


interface Core_Admin_EntityManager_Common_Interface extends Admin_EntityManager_Interface {
	
	public static function showActiveState( int $id ): string;
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface;
	
	public static function getEntityNameReadable() : string;
	
}