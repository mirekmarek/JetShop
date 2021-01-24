<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Application_Module;
use JetShop\Admin_Module_Trait;
use JetShop\Auth_Administrator_Role;
use JetShop\Product_ManageModuleInterface;
use Jet\Auth;
use Jet\Mvc_Page;


class Main extends Application_Module implements Product_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'products';

	const ACTION_GET_PRODUCT = 'get_product';
	const ACTION_ADD_PRODUCT = 'add_product';
	const ACTION_UPDATE_PRODUCT = 'update_product';
	const ACTION_DELETE_PRODUCT = 'delete_product';

	public function getProductSelectWhispererUrl( array $only_types=[], bool $only_active=false ) : string
	{
		$only_types = implode(',', $only_types);

		$page = Mvc_Page::get( static::ADMIN_MAIN_PAGE );
		if(!$page) {
			return '';
		}

		return $page->getURL([], [
			'only_types' => $only_types,
			'only_active' => $only_active ? 1:0
		]);

	}

	public function getProductEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_PRODUCT,
			static::ACTION_UPDATE_PRODUCT,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditProduct() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_PRODUCT );
	}

	public static function getCurrentUserCanCreateProduct() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_PRODUCT );
	}
}