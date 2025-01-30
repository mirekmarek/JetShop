<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Form;
use JetApplication\Admin_EntityManager_Interface;

interface Core_Entity_Admin_Interface {
	
	public function getAdminManager() : null|Application_Module|Admin_EntityManager_Interface;
	public function getAdminTitle(): string;
	
	public function isEditable(): bool;
	public function setEditable( bool $editable ): void;
	
	public function getEditUrl( array $get_params=[] ) : string;
	
	public function getAddForm() : Form;
	public function catchAddForm() : bool;
	
	public function getEditForm() : Form;
	public function catchEditForm() : bool;
	
	public function getEditMainForm() : Form;
	public function catchEditMainForm() : bool;
	
	public function getAdminManagerInterface() : ?string;
	
	public function renderActiveState() : string;
	
}