<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
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
	abstract public function uploadFile( string $entity_type, int $entity_id, string $file_name, string $srouce_file_path ): string;
	
	abstract public function getFilePath( string $entity_type, int $entity_id, string $file ) : string;
	
	abstract public function getFileURL( string $entity_type, int $entity_id, string $file ) : string;
	
	abstract public function deleteFile( string $entity_type, int $entity_id, string $file ): void;
}