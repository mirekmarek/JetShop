<?php
namespace JetShop;


abstract class Core_ProductListing_Sort_Option {

	protected string $id = '';

	protected string $label = '';

	protected string $url_param = '';

	protected bool $is_default = false;

	protected bool $is_active = false;

	/**
	 * @var callable
	 */
	protected $sorter;

	public function __construct( string $id )
	{
		$this->id = $id;
	}

	public function getId() : string
	{
		return $this->id;
	}

	public function getLabel() : string
	{
		return $this->label;
	}

	public function setLabel( string $label ) : void
	{
		$this->label = $label;
	}

	public function getUrlParam() : string
	{
		return $this->url_param;
	}

	public function setUrlParam( string $url_param ) : void
	{
		$this->url_param = $url_param;
	}

	public function isDefault() : bool
	{
		return $this->is_default;
	}

	public function setIsDefault( bool $is_default ) : void
	{
		$this->is_default = $is_default;
	}

	public function isActive() : bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function getSorter() : callable
	{
		return $this->sorter;
	}

	public function setSorter( callable $sorter ) : void
	{
		$this->sorter = $sorter;
	}

}