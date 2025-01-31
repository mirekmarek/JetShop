<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


interface Core_EShop_Managers_Image
{
	
	public function getUrl( string $image ) : string;
	
	public function getThumbnailUrl( string $image, int $max_w, int $max_h ) : string;
	
}