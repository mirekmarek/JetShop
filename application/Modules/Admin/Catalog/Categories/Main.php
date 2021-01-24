<?php
namespace JetShopModule\Admin\Catalog\Categories;

use Jet\Application_Module;
use JetShop\Admin_Module_Trait;
use JetShop\Auth_Administrator_Role;
use JetShop\Category_ManageModuleInterface;
use Jet\Auth;
use Jet\Mvc_Page;

/**
 *
 */
class Main extends Application_Module implements Category_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'categories';

	const ACTION_GET_CATEGORY = 'get_category';
	const ACTION_ADD_CATEGORY = 'add_category';
	const ACTION_UPDATE_CATEGORY = 'update_category';
	const ACTION_DELETE_CATEGORY = 'delete_category';

	public function getCategorySelectWhispererUrl( array $only_types=[], int $exclude_branch_id=0, bool $only_active=false ) : string
	{
		$only_types = implode(',', $only_types);
		$exclude_branch_id = (int)$exclude_branch_id;

		$page = Mvc_Page::get( static::ADMIN_MAIN_PAGE );
		if(!$page) {
			return '';
		}

		return $page->getURL([], [
			'only_types' => $only_types,
			'exclude_branch_id' => $exclude_branch_id,
			'only_active' => $only_active ? 1:0
		]);

	}

	public function getCategoryEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_CATEGORY,
			static::ACTION_UPDATE_CATEGORY,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public function getParametrizationEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_CATEGORY,
			static::ACTION_UPDATE_CATEGORY,
			static::ADMIN_MAIN_PAGE,
			$id,
			[
				'page' => 'parametrization'
			]
		);
	}

	public function getParametrizationGroupEditUrl( int $category_id, int $group_id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_CATEGORY,
			static::ACTION_UPDATE_CATEGORY,
			static::ADMIN_MAIN_PAGE,
			$category_id,
			[
				'group_id' => $group_id
			]
		);
	}

	public function getParametrizationPropertyEditUrl( int $category_id, int $group_id, int $property_id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_CATEGORY,
			static::ACTION_UPDATE_CATEGORY,
			static::ADMIN_MAIN_PAGE,
			$category_id,
			[
				'group_id' => $group_id,
				'property_id' => $property_id
			]
		);
	}

	public function getParametrizationOptionEditUrl( int $category_id, int $group_id, int $property_id, int $option_id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_CATEGORY,
			static::ACTION_UPDATE_CATEGORY,
			static::ADMIN_MAIN_PAGE,
			$category_id,
			[
				'group_id' => $group_id,
				'property_id' => $property_id,
				'option_id' => $option_id
			]
		);

	}

	public static function getCurrentUserCanEditCategory() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_CATEGORY );
	}

	public static function getCurrentUserCanCreateCategory() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_CATEGORY );
	}
}