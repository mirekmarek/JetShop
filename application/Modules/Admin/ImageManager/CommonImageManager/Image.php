<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ImageManager;


use Jet\Data_Image;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\SysConf_URI;

class CommonImageManager_Image {
	protected string $id;
	protected string $name;
	protected string $URL;
	protected string $path;
	protected Data_Image $image;
	
	public function __construct( string $entity, int $entity_id, string $name ) {
		
		$this->id = md5($name);
		$this->path = SysConf_Path::getImages().$entity.'/'.$entity_id.'/'.$name;
		$this->name = $name;
		$this->URL = SysConf_URI::getImages().$entity.'/'.$entity_id.'/'.rawurlencode($name);
		$this->image = new Data_Image($this->path);
	}
	
	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}
	
	
	
	public function getName(): string
	{
		return $this->name;
	}
	
	
	public function getURL(): string
	{
		return $this->URL;
	}
	
	public function getFileSize() : int
	{
		return IO_File::getSize( $this->path );
	}
	
	public function getWidth() : int
	{
		return $this->image->getWidth();
	}
	public function getHeight() : int
	{
		return $this->image->getHeight();
	}
	
}