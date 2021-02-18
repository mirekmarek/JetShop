<?php
namespace JetShop;

abstract class Core_CashDesk_AgreeFlag
{
	protected string $code = '';

	protected string $title = '';

	protected string $detail_description = '';

	protected string $error_message = '';

	protected bool $is_mandatory = false;

	protected bool $is_checked = false;

	protected bool $default_checked = false;

	protected bool $show_error = false;

	/**
	 * @var callable
	 */
	protected $order_state_setter = null;

	public function __construct( string $code, string $title, string $detail_description='', bool $is_mandatory=false )
	{
		$this->code = $code;
		$this->title = $title;
		$this->detail_description = $detail_description;
		$this->is_mandatory = $is_mandatory;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode( string $code ): void
	{
		$this->code = $code;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle( string $title ): void
	{
		$this->title = $title;
	}

	public function getDetailDescription(): string
	{
		return $this->detail_description;
	}

	public function setDetailDescription( string $detail_description ): void
	{
		$this->detail_description = $detail_description;
	}

	public function isMandatory(): bool
	{
		return $this->is_mandatory;
	}

	public function setIsMandatory( bool $is_mandatory ): void
	{
		$this->is_mandatory = $is_mandatory;
	}

	public function isChecked(): bool
	{
		return $this->is_checked;
	}

	public function setIsChecked( bool $is_checked ): void
	{
		$this->is_checked = $is_checked;
	}

	public function isDefaultChecked(): bool
	{
		return $this->default_checked;
	}

	public function setDefaultChecked( bool $default_checked ): void
	{
		$this->default_checked = $default_checked;
	}

	public function getErrorMessage(): string
	{
		return $this->error_message;
	}

	public function setErrorMessage( string $error_message ): void
	{
		$this->error_message = $error_message;
	}

	public function showError(): bool
	{
		return $this->show_error;
	}

	public function setShowError( bool $show_error ): void
	{
		$this->show_error = $show_error;
	}

	public function getOrderStateSetter(): ?callable
	{
		return $this->order_state_setter;
	}

	public function setOrderStateSetter( ?callable $order_state_setter ): void
	{
		$this->order_state_setter = $order_state_setter;
	}

	public function setOrderState( Order $order )
	{
		if(!$this->order_state_setter) {
			return;
		}

		$setter = $this->order_state_setter;

		$setter( $order, $this->isChecked() );
	}

}