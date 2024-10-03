<?php
namespace JetShop;

use JetApplication\Files_Manager;
use JetApplication\Managers_General;

abstract class Core_Files {
	
	public static function Manager() : Files_Manager
	{
		return Managers_General::Files();
	}

}