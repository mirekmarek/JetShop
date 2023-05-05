<?php
namespace JetShop;

interface Core_PropertyGroup_ManageModuleInterface {
	
	public function getPropertyGroupEditUrl( int $id ) : string;
	
	public function getPropertyGroupSelectWhispererUrl( $only_active=false ) : string;
}