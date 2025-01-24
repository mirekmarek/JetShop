<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Form;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Entity_WithEShopData_EShopData;
use JetApplication\EShop;

interface Core_Admin_Entity_WithEShopData_Interface extends Admin_Entity_Interface {
	
	public function getAdminManager() : null|Application_Module|Admin_EntityManager_WithEShopData_Interface;
	
	public function defineImages() : void;
	
	public function handleImages() : void;
	
	public function uploadImage(
		string $image_class,
		string $tmp_file_path,
		string $file_name,
		EShop $eshop
	) : void;
	
	
	public static function getEntityShopDataInstance() : Entity_WithEShopData_EShopData;
	
	public function getDescriptionEditForm() : Form;
	
	public function getDescriptionEditFormFieldMap() : array;
	
	public function catchDescriptionEditForm() : bool;
}