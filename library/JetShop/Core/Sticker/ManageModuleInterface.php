<?php
namespace JetShop;

interface Core_Sticker_ManageModuleInterface {

	public function getStickerEditUrl( string $code ) : string;

	public static function getCurrentUserCanEditSticker() : bool;

	public static function getCurrentUserCanCreateSticker() : bool;
}