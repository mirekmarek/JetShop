<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\FilesManager;

use Jet\Application_Module;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\Entity_Common;
use JetApplication\Entity_WithEShopData;
use JetApplication\Entity_WithEShopData_EShopData;
use JetApplication\Files_Manager;

/**
 *
 */
class Main extends Application_Module implements Files_Manager
{
	public function deleteFile( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity, string $file ): void
	{
		
		$path = $this->getFilePath( $entity, $file );
		if(IO_File::exists($path)) {
			IO_File::delete( $path );
		}
	}
	
	
	public function uploadFile( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity, string $file_name, string $srouce_file_paths ): string
	{
		
		$path = $this->getFilePath( $entity, $file_name );
		IO_File::copy(
			source_path: $srouce_file_paths,
			target_path: $path,
			overwrite_if_exists: true
		);
		
		return $file_name;
	}
	
	public function getFilePath( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity, string $file ): string
	{
		return $this->getDirPath($entity).$file;
	}
	
	public function getFileURL( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity, string $file ): string
	{
		return SysConf_URI::getBase().$this->getDirName($entity).rawurlencode($file);
	}
	
	public function getDirPath( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity  ) : string
	{
		$dir_name = $this->getDirName( $entity );
		$dir_path = SysConf_Path::getBase().$dir_name;
		
		if(!IO_Dir::exists($dir_path)) {
			IO_Dir::create( $dir_path );
		}
		
		return $dir_path;
	}
	
	protected function getDirName( Entity_WithEShopData_EShopData|Entity_Common|Entity_WithEShopData $entity ) : string
	{
		$id = $entity->getId();
		
		return 'files/'.$entity::getEntityType().'/'.$this->calcNumericPath( $id ).'/';
	}
	
	protected function calcNumericPath( int $object_id ) : string
	{
		$map = [];
		
		$numerical_order = 100000;
		
		$number = $object_id;
		while( $numerical_order>=10 ) {
			$c = floor($number/$numerical_order);
			
			$map[$numerical_order] = $c;
			
			$number = $number-($c*$numerical_order);
			
			$numerical_order = $numerical_order/10;
		}
		
		return implode('/',$map);
	}
	
}