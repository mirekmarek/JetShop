<?php
namespace JetShop;

interface Core_Services_Service_ManageModuleInterface {

	public function getServiceEditURL( string $id ) : string;

	public static function getCurrentUserCanEditService() : bool;

	public static function getCurrentUserCanCreateService() : bool;

}