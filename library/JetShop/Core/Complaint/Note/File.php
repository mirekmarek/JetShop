<?php
namespace JetShop;

use Jet\BaseObject;
use Jet\Http_Request;
use Jet\IO_File;

abstract class Core_Complaint_Note_File extends BaseObject
{
	protected string $name = '';
	protected string $path = '';
	protected string $mime = '';
	protected int $size = 0;
	protected string $download_URL = '';
	protected bool $is_image = false;
	
	public function __construct( string $base_path, string $file_name )
	{
		$this->name = $file_name;
		$this->path = $base_path.$file_name;
		if(IO_File::isReadable($this->path)) {
			$this->mime = IO_File::getMimeType( $this->path );
			$this->size = IO_File::getSize( $this->path );
		}
		
		$this->download_URL = Http_Request::currentURI(['note-action'=>'show_note_file', 'file'=>$file_name]);
		$this->is_image = str_contains( $this->mime, 'image' );
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getPath(): string
	{
		return $this->path;
	}
	
	public function getMime(): string
	{
		return $this->mime;
	}
	
	public function getSize(): int
	{
		return $this->size;
	}
	
	public function getDownloadURL(): string
	{
		return $this->download_URL;
	}
	
	public function isImage(): bool
	{
		return $this->is_image;
	}
	
	
}