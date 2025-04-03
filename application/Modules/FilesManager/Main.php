<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\FilesManager;

use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\Files_Manager;


class Main extends Files_Manager
{
	public function deleteFile( string $entity_type, int $entity_id,  string $file ): void
	{
		
		$path = $this->getFilePath( $entity_type, $entity_id, $file );
		if(IO_File::exists($path)) {
			IO_File::delete( $path );
		}
	}
	
	
	public function uploadFile( string $entity_type, int $entity_id,  string $file_name, string $srouce_file_path ): string
	{
		
		$path = $this->getFilePath( $entity_type, $entity_id, $file_name );
		IO_File::copy(
			source_path: $srouce_file_path,
			target_path: $path,
			overwrite_if_exists: true
		);
		
		return $file_name;
	}
	
	public function getFilePath( string $entity_type, int $entity_id,  string $file ): string
	{
		return $this->getDirPath($entity_type, $entity_id).$file;
	}
	
	public function getFileURL( string $entity_type, int $entity_id,  string $file ): string
	{
		return SysConf_URI::getBase().$this->getDirName($entity_type, $entity_id).rawurlencode($file);
	}
	
	public function getDirPath( string $entity_type, int $entity_id ) : string
	{
		$dir_name = $this->getDirName( $entity_type, $entity_id );
		$dir_path = SysConf_Path::getBase().$dir_name;
		
		if(!IO_Dir::exists($dir_path)) {
			IO_Dir::create( $dir_path );
		}
		
		return $dir_path;
	}
	
	protected function getDirName( string $entity_type, int $entity_id ) : string
	{
		return 'files/'.$entity_type.'/'.$this->calcNumericPath( $entity_id ).'/';
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