<?php
namespace JetApplication;

interface Admin_Managers_Image
{
	public function setShopSyncMode( bool $shop_sync_mode ): void;
	
	public function getShopSyncMode(): bool;
	
	public function defineImage(
		string $entity,
		string|int $object_id,
		?string $image_class='',
		?string $image_title='',
		?callable $image_property_getter=null,
		?callable $image_property_setter=null,
		?Shops_Shop $shop=null
	);
	
	public function getEditable(): bool;
	
	public function setEditable( bool $editable ): void;
	
	public function handleSelectImageWidgets() : bool;
	
	public function renderMain() : string;
	
	public function renderImageWidgets( ?Shops_Shop $shop=null ) : string;
	
	public function renderStandardManagement() : string;
	
	public function handleProductImageManagement( Product $product ) : void;
	
	public function renderProductImageManagement() : string;
}