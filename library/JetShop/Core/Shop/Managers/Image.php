<?php
namespace JetShop;

interface Core_Shop_Managers_Image
{
	
	public function getUrl( string $image ) : string;
	
	public function getThumbnailUrl( string $image, int $max_w, int $max_h ) : string;
	
}