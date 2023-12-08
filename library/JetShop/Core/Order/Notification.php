<?php
/**
 *
 */

namespace JetShop;


use JetApplication\Entity_WithShopRelation;

abstract class Core_Order_Notification extends Entity_WithShopRelation
{

	protected int $customer_id = 0;

	protected int $order_id = 0;

	protected string $kind = '';

	protected string $view_root_dir = '';

	protected string $text_view_script = '';

	protected array $view_data = [];

	public function getCustomerId(): int
	{
		return $this->customer_id;
	}

	public function setCustomerId( int $customer_id ): void
	{
		$this->customer_id = $customer_id;
	}

	public function getOrderId(): int
	{
		return $this->order_id;
	}

	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}

	public function getKind(): string
	{
		return $this->kind;
	}

	public function setKind( string $kind ): void
	{
		$this->kind = $kind;
	}

	public function getViewRootDir(): string
	{
		return $this->view_root_dir;
	}

	public function setViewRootDir( string $view_root_dir ): void
	{
		$this->view_root_dir = $view_root_dir;
	}

	public function getTextViewScript(): string
	{
		return $this->text_view_script;
	}

	public function setTextViewScript( string $text_view_script ): void
	{
		$this->text_view_script = $text_view_script;
	}

	public function getViewData(): array
	{
		return $this->view_data;
	}

	public function setViewData( string $key, mixed $data ): void
	{
		$this->view_data[$key] = $data;
	}


}