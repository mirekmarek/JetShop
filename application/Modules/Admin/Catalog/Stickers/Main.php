<?php
namespace JetShopModule\Admin\Catalog\Stickers;

use JetShop\Sticker_ManageModuleInterface;
use Jet\Application_Module;
use JetShop\Auth_Administrator_Role;
use Jet\Auth;

/**
 *
 */
class Main extends Application_Module implements Sticker_ManageModuleInterface
{
	const ADMIN_MAIN_PAGE = 'stickers';

	const ACTION_GET_STICKER = 'get_sticker';
	const ACTION_ADD_STICKER = 'add_sticker';
	const ACTION_UPDATE_STICKER = 'update_sticker';
	const ACTION_DELETE_STICKER = 'delete_sticker';


	public function getStickerEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_STICKER,
			static::ACTION_UPDATE_STICKER,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditSticker() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_STICKER );
	}

	public static function getCurrentUserCanCreateSticker() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_STICKER );
	}

}