<?php
namespace JetShop;

use JetApplication\Entity_Common;
use JetApplication\Entity_WithEShopData;
use JetApplication\Entity_WithEShopData_EShopData;

interface Core_Files_Manager
{
	public function uploadFile( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity, string $file_name, string $srouce_file_paths ): string;
	
	public function getFilePath( Entity_Common|Entity_WithEShopData|Entity_WithEShopData_EShopData $entity, string $file ) : string;
	
	public function getFileURL( Entity_Common|Entity_WithEShopData|Entity_WithEShopData_EShopData $entity, string $file ) : string;
	
	public function deleteFile( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity, string $file ): void;
}