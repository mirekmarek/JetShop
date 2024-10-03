<?php
namespace JetShop;

interface Core_Shop_Managers_Banners {
	
	public function renderPosition( string $banner_group_code ) : string;
	
}