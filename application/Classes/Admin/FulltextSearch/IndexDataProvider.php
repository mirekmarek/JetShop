<?php
namespace JetApplication;

interface Admin_FulltextSearch_IndexDataProvider {
	
	public function getAdminFulltextObjectClass() : string;
	public function getAdminFulltextObjectId() : string;
	public function getAdminFulltextObjectType() : string;
	public function getAdminFulltextObjectIsActive() : bool;
	public function getAdminFulltextObjectTitle() : string;
	public function getAdminFulltextTexts() : array;
	
}