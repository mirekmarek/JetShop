<?php
namespace JetShop;

abstract class Core_Carrier_Document {
	
	protected string $mime_type;
	protected string $data;
	protected string $notes = '';
	
	public function __construct( string $mime_type='', string $data='' )
	{
		$this->mime_type = $mime_type;
		$this->data = $data;
	}

	public function getMimeType(): string
	{
		return $this->mime_type;
	}
	
	public function setMimeType( string $mime_type ): void
	{
		$this->mime_type = $mime_type;
	}

	public function getData(): string
	{
		return $this->data;
	}
	

	public function setData( string $data ): void
	{
		$this->data = $data;
	}

	public function getNotes(): string
	{
		return $this->notes;
	}

	public function setNotes( string $notes ): void
	{
		$this->notes = $notes;
	}
	
	public function show() : void
	{
		header("Content-Type: ".$this->mime_type);
		echo $this->data;
		die();
		
	}
	
}