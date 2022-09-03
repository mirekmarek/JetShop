<?php
namespace JetShop;

abstract class Core_ProductListing_Filter_Flags_Flag {

	/**
	 * @var ProductListing|null
	 */
	protected ?ProductListing $listing = null;

	protected string $id = '';

	protected string $label = '';

	protected string $url_param = '';

	protected bool $is_active = false;

	protected bool $is_disabled = false;

	protected array $select_items = [];

	protected array $product_ids = [];

	protected ?int $_count = null;

	/**
	 * @var callable
	 */
	protected $analyzer;

	public function __construct(  ProductListing $listing, $id )
	{
		$this->listing = $listing;

		$this->id = $id;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function setLabel( string $label ) : void
	{
		$this->label = $label;
	}

	public function getUrlParam(): string
	{
		return $this->url_param;
	}

	public function setUrlParam( string $url_param ) : void
	{
		$this->url_param = $url_param;
	}

	public function isActive(): bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function isDisabled(): bool
	{
		return $this->is_disabled;
	}

	public function setIsDisabled( bool $is_disabled ) : void
	{
		$this->is_disabled = $is_disabled;
	}

	public function getProductIds(): array
	{
		return $this->product_ids;
	}

	public function setProductIds( array $product_ids ) : void
	{
		$this->product_ids = $product_ids;
	}

	public function getCount(): ?int
	{
		if($this->_count === null) {
			$ids = $this->listing->internalGetFilteredProductIds( $this->product_ids, 'flags' );

			$this->_count = count($ids);
		}

		return $this->_count;
	}

	public function resetCount() : void
	{
		$this->_count = null;
	}

	public function generateURLWithout(): string
	{
		$active = $this->is_active;

		$this->is_active = false;

		$url = $this->listing->generateUrl();

		$this->is_active = $active;

		return $url;
	}

	public function generateURLWith(): string
	{
		$active = $this->is_active;

		$this->is_active = true;

		$url = $this->listing->generateUrl();

		$this->is_active = $active;

		return $url;
	}

	public function getSelectItems(): array
	{
		return $this->select_items;
	}

	public function setSelectItems( array $select_items ): void
	{
		$this->select_items = $select_items;
	}

	public function getAnalyzer(): callable
	{
		return $this->analyzer;
	}

	public function setAnalyzer( callable $analyzer ): void
	{
		$this->analyzer = $analyzer;
	}

	public function addToMap( array $item ) : bool
	{
		$analyzer = $this->analyzer;

		if($analyzer($item)) {
			return true;
		}

		return false;
	}

}