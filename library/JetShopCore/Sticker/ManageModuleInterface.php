<?php
namespace JetShop;

interface Core_Sticker_ManageModuleInterface {

	public function getStickerEditUrl( int $id ) : string;

	public static function getCurrentUserCanEditSticker() : bool;

	public static function getCurrentUserCanCreateSticker() : bool;
}