<?php
namespace JetShop;

use JetApplication\Order;
use JetApplication\CashDesk;

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

	/**
	 * @var callable
	 */
	protected $on_customer_login = null;

	/**
	 * @var callable
	 */
	protected $on_customer_logout = null;

	/**
	 * @var callable
	 */
	protected $on_order_save = null;


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

	public function setOrderState( Order $order ) : void
	{
		if(!$this->order_state_setter) {
			return;
		}

		$setter = $this->order_state_setter;

		$setter( $order, $this->isChecked() );
	}

	public function getOnCustomerLogin(): ?callable
	{
		return $this->on_customer_login;
	}


	public function setOnCustomerLogin( callable $on_customer_login ): void
	{
		$this->on_customer_login = $on_customer_login;
	}


	public function onCustomerLogin( CashDesk $cash_desk ) : void
	{
		if(!$this->on_customer_login) {
			return;
		}

		$handler = $this->on_customer_login;

		$handler( $cash_desk, $this->isChecked() );
	}

	public function getOnCustomerLogout(): ?callable
	{
		return $this->on_customer_logout;
	}

	public function setOnCustomerLogout( callable $on_customer_logout ): void
	{
		$this->on_customer_logout = $on_customer_logout;
	}


	public function onCustomerLogout( CashDesk $cash_desk ) : void
	{
		if(!$this->on_customer_logout) {
			return;
		}

		$handler = $this->on_customer_logout;

		$handler( $cash_desk, $this->isChecked() );
	}

	public function getOnOrderSave(): ?callable
	{
		return $this->on_order_save;
	}

	public function setOnOrderSave( callable $on_order_save ): void
	{
		$this->on_order_save = $on_order_save;
	}


	public function onOrderSave( Order $order ) : void
	{
		if(!$this->on_order_save) {
			return;
		}

		$handler = $this->on_order_save;

		$handler( $order, $this->isChecked() );
	}

}