<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_EShopData;

interface Core_Files_Manager
{
	public function uploadFile( EShopEntity_WithEShopData_EShopData|EShopEntity_Common|EShopEntity_WithEShopData $entity, string $file_name, string $srouce_file_paths ): string;
	
	public function getFilePath( EShopEntity_Common|EShopEntity_WithEShopData|EShopEntity_WithEShopData_EShopData $entity, string $file ) : string;
	
	public function getFileURL( EShopEntity_Common|EShopEntity_WithEShopData|EShopEntity_WithEShopData_EShopData $entity, string $file ) : string;
	
	public function deleteFile( EShopEntity_WithEShopData_EShopData|EShopEntity_Common|EShopEntity_WithEShopData $entity, string $file ): void;
}