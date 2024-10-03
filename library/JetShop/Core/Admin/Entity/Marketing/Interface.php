<?php
namespace JetShop;

use JetApplication\Admin_Entity_Interface;

interface Core_Admin_Entity_Marketing_Interface extends Admin_Entity_Interface {

	public function hasImages() : bool;
	
	public function handleImages() : void;
	
	public function defineImage( string $image_class, string $image_title ) : void;
}