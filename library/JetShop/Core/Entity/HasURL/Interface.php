<?php
namespace JetShop;


interface Core_Entity_HasURL_Interface {
	
	public function getURL( array $GET_params=[] ) : string;
	public function checkURL( string $URL_path ) : bool;
	
	public function generateURLPathPart() : string;
	public function getURLNameDataSource() : string;

}