<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ImageManager;

use Jet\Data_Image;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\EShop_Managers_Image;

class Main extends EShop_Managers_Image
{
	protected string $thb_dir = '_thb';
	
	protected ?string $root_path = null;
	
	protected ?string $root_url = null;
	
	
	public function getRootPath(): string
	{
		if(!$this->root_path) {
			$this->root_path = SysConf_Path::getImages();
		}
		
		return $this->root_path;
	}
	
	public function setRootPath( string $root_path ) : void
	{
		$this->root_path = $root_path;
	}
	
	
	public function getThbDir() : string
	{
		return $this->thb_dir;
	}
	
	public function setThbDir( string $thb_dir ) : void
	{
		$this->thb_dir = $thb_dir;
	}
	
	public function getRootUrl() : string
	{
		if(!$this->root_url) {
			$this->root_url = SysConf_URI::getImages();
		}
		
		return $this->root_url;
	}
	
	public function setRootUrl( string $root_url ) : void
	{
		$this->root_url = $root_url;
	}
	
	
	public function getThbRootDir( string $file_name ) : string
	{
		
		$dir = static::getThbDir().'/'.dirname( $file_name ).'/';
		
		$full_path = static::getRootPath().$dir;
		if(!IO_Dir::exists($full_path)) {
			IO_Dir::create( $full_path );
		}
		
		return $dir;
	}
	
	
	public function getUrl( string $image ): string
	{
		if(!$image) {
			return '';
		}
		
		return static::getRootUrl().$image;
	}
	
	public function getThumbnailUrl( string $image, int $max_w, int $max_h ): string
	{
		if(!$image) {
			return '';
		}
		
		$thb_source_path = static::getRootPath().$image;
		if(!IO_File::exists($thb_source_path)) {
			return '';
		}
		
		
		$thb_path = $this->getThbRootDir($image).$max_w.'x'.$max_h.'__'.basename($image);
		
		$thb_target_path = static::getRootPath().$thb_path;
		
		$url = static::getRootUrl().$thb_path;
		
		
		if(!IO_File::exists($thb_target_path)) {
			$target_dir = dirname($thb_target_path);
			if(!IO_Dir::exists($target_dir)) {
				IO_Dir::create( $target_dir );
			}
			
			$image = new Data_Image( $thb_source_path );
			$image->createThumbnail( $thb_target_path, $max_w, $max_h );
		}
		
		
		return $url;
		
	}
	
	public function getPath( string $image ): string
	{
		return static::getRootPath().$image;
	}
}