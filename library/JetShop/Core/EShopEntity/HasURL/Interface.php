<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



interface Core_EShopEntity_HasURL_Interface {
	
	public function getURL( array $GET_params=[] ) : string;
	public function checkURL( string $URL_path ) : bool;
	
	public function generateURLPathPart() : string;
	public function getURLNameDataSource() : string;
	
	public static function getIdByURLPathPart( ?string $URL_path ) : ?int;

}