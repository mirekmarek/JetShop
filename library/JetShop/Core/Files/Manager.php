<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Files',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Files_Manager extends Application_Module
{
	abstract public function uploadFile( EShopEntity_WithEShopData_EShopData|EShopEntity_Common|EShopEntity_WithEShopData $entity, string $file_name, string $srouce_file_paths ): string;
	
	abstract public function getFilePath( EShopEntity_Common|EShopEntity_WithEShopData|EShopEntity_WithEShopData_EShopData $entity, string $file ) : string;
	
	abstract public function getFileURL( EShopEntity_Common|EShopEntity_WithEShopData|EShopEntity_WithEShopData_EShopData $entity, string $file ) : string;
	
	abstract public function deleteFile( EShopEntity_WithEShopData_EShopData|EShopEntity_Common|EShopEntity_WithEShopData $entity, string $file ): void;
}