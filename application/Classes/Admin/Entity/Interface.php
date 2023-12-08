<?php
namespace JetApplication;

use Jet\Form;
use Jet\DataModel_Fetch_Instances;

interface Admin_Entity_Interface {
	
	public static function get( int|string $id_or_code ) : static|null;
	
	public function isEditable(): bool;
	public function setEditable( bool $editable ): void;
	
	
	public function checkShopData() : void;
	
	public function getAddForm() : Form;
	public function catchAddForm() : bool;
	
	public function getEditForm() : Form;
	public function catchEditForm() : bool;
	
	public function handleImages() : void;
	public function defineImages() : void;
	
	/**
	 *
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getList() : DataModel_Fetch_Instances|iterable;
	
}