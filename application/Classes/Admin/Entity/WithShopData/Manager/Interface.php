<?php
namespace JetApplication;


interface Admin_Entity_WithShopData_Manager_Interface extends Admin_Entity_Common_Manager_Interface {
	
	public function renderActiveState( Entity_WithShopData $item ) : string;
}