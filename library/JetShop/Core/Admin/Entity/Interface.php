<?php
namespace JetShop;

use Jet\Form;

interface Core_Admin_Entity_Interface {
	
	public static function get( int|string $id ) : static|null;
	
	public function isEditable(): bool;
	public function setEditable( bool $editable ): void;
	
	public function getEditURL( array $get_params=[] ) : string;
	
	public function getAddForm() : Form;
	public function catchAddForm() : bool;
	
	public function getEditForm() : Form;
	public function catchEditForm() : bool;
	
	public function getAdminManagerInterface() : ?string;
	
}