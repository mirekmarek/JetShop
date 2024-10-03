<?php
namespace JetShop;

use Jet\Form_Field_File_UploadedFile;
use JetApplication\Entity_Common;
use JetApplication\Entity_WithShopData;
use JetApplication\Entity_WithShopData_ShopData;

interface Core_Files_Manager
{
	public function uploadFile( Entity_Common|Entity_WithShopData|Entity_WithShopData_ShopData $entity, Form_Field_File_UploadedFile $file ) : string;
	
	public function getFilePath( Entity_Common|Entity_WithShopData|Entity_WithShopData_ShopData $entity, string $file ) : string;
	
	public function getFileURL( Entity_Common|Entity_WithShopData|Entity_WithShopData_ShopData $entity, string $file ) : string;
	
	public function deleteFile( Entity_WithShopData_ShopData|Entity_Common|Entity_WithShopData $entity, string $file ): void;
}