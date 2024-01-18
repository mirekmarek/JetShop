<?php
namespace JetApplication;

use Jet\Form;
use Jet\DataModel_Fetch_Instances;

interface Admin_Entity_Common_Interface extends Admin_FulltextSearch_IndexDataProvider {
	
	public static function get( int $id ) : static|null;
	
	public function isEditable(): bool;
	public function setEditable( bool $editable ): void;
	public function getEditURL() : string;
	
	public function getAddForm() : Form;
	public function catchAddForm() : bool;
	
	public function getEditForm() : Form;
	public function catchEditForm() : bool;
	
	public function getAdminTitle() : string;
	
	/**
	 *
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getList() : DataModel_Fetch_Instances|iterable;
	
	public function isItPossibleToDelete() : bool;
	
}