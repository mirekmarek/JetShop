<?php
namespace JetShop;

use Jet\Form;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Entity_WithEShopData_EShopData;

interface Core_Admin_Entity_WithEShopData_Interface extends Admin_Entity_Interface {
	
	public static function getEntityShopDataInstance() : Entity_WithEShopData_EShopData;
	
	public function getDescriptionEditForm() : Form;
	
	public function getDescriptionEditFormFieldMap() : array;
	
	public function catchDescriptionEditForm() : bool;
}