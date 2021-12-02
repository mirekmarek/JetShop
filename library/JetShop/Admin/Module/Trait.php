<?php
namespace JetShop;


use Jet\Auth;
use Jet\MVC;

trait Admin_Module_Trait {

	protected function getEditUrl( string $get_action, string $edit_action, string $page_id, int|string $object_id, array $get_params=[] ) : string
	{
		if( !Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, $get_action ) ) {
			return '';
		}

		$page = MVC::getPage( $page_id );
		if(!$page) {
			return '';
		}

		if(is_string($object_id)) {
			$get_params['code'] = $object_id;
		} else {
			$get_params['id'] = $object_id;
		}

		return $page->getURL([], $get_params);

	}
}