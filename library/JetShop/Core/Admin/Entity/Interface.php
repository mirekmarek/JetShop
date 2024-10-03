<?php
namespace JetShop;

use Jet\Form;

interface Core_Admin_Entity_Interface {
	
	public static function get( int $id ) : static|null;
	
	public function isEditable(): bool;
	public function setEditable( bool $editable ): void;
	
	public function getEditURL() : string;
	
	public function getAddForm() : Form;
	public function catchAddForm() : bool;
	
	public function getEditForm() : Form;
	public function catchEditForm() : bool;
	
}