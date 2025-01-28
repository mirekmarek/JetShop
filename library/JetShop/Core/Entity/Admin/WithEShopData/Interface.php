<?php
namespace JetShop;

use Jet\Form;
use JetApplication\Entity_Admin_Interface;

interface Core_Entity_Admin_WithEShopData_Interface extends Entity_Admin_Interface {
	
	public function getDescriptionMode() : bool;
	
	public function getDescriptionEditFormFieldMap() : array;
	
	public function getDescriptionEditForm() : Form;
	
	public function catchDescriptionEditForm() : bool;
}