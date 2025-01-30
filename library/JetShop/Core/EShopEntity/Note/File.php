<?php
namespace JetShop;

use Jet\BaseObject;
use Jet\Http_Request;
use Jet\IO_File;

abstract class Core_EShopEntity_Note_File extends BaseObject
{
	protected int $note_id = 0;
	protected string $name = '';
	protected string $path = '';
	protected string $mime = '';
	protected int $size = 0;
	protected bool $is_image = false;
	
	public function __construct( string $base_path, string $file_name )
	{
		$this->name = $file_name;
		$this->path = $base_path.$file_name;
		if(IO_File::isReadable($this->path)) {
			$this->mime = IO_File::getMimeType( $this->path );
			$this->size = IO_File::getSize( $this->path );
		}
		
		$this->is_image = str_contains( $this->mime, 'image' );
	}
	
	public function getNoteId(): int
	{
		return $this->note_id;
	}
	
	public function setNoteId( int $note_id ): void
	{
		$this->note_id = $note_id;
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
		return Http_Request::currentURI(['note-action'=>'show_note_file', 'file'=>$this->name, 'note'=>$this->note_id]);
	}
	
	public function isImage(): bool
	{
		return $this->is_image;
	}
	
	
}