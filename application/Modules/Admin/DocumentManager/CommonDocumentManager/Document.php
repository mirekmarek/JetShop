<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\DocumentManager;

use Jet\IO_File;

class CommonDocumentManager_Document {
	protected string $id;
	protected string $name;
	protected string $URL;
	protected string $path;
	
	public function __construct( string $entity, int $entity_id, string $name ) {
		
		$this->id = md5($name);
		$this->path = Main::getRootDirectoryPath().$entity.'/'.$entity_id.'/'.$name;
		$this->name = $name;
		$this->URL = Main::getRootDirectoryURI().$entity.'/'.$entity_id.'/'.rawurlencode($name);
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
	
	public function getFileMimeType() : string
	{
		return IO_File::getMimeType( $this->path );
	}
	
}