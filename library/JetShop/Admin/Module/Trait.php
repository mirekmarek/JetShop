<?php
namespace JetShop;


use Jet\Auth;
use Jet\Mvc_Page;

trait Admin_Module_Trait {

	protected function getEditUrl( string $get_action, string $edit_action, string $page_id, int $object_id, array $get_params=[] ) : string
	{
		if( !Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, $get_action ) ) {
			return '';
		}

		$page = Mvc_Page::get( $page_id );
		if(!$page) {
			return '';
		}

		$get_params['id'] = $object_id;

		return $page->getURL([], $get_params);

	}
}