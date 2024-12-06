<?php
namespace JetShop;

interface Core_EShop_Managers_Banners {
	
	public function renderPosition( string $banner_group_code ) : string;
	
}