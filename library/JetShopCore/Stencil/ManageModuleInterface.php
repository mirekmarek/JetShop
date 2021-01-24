<?php
namespace JetShop;

interface Core_Stencil_ManageModuleInterface {

	public function getStencilEditUrl( int $id ) : string;

	public static function getCurrentUserCanEditStencil() : bool;

	public static function getCurrentUserCanCreateStencil() : bool;
}