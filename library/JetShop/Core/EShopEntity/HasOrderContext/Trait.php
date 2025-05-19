<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Order;

trait Core_EShopEntity_HasOrderContext_Trait
{
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $order_number = '';
	
	public function setOrder( Order $order ) : void
	{
		$this->setOrderId( $order->getId() );
		$this->setOrderNumber( $order->getNumber() );
	}
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}
	
	
	public function getOrderNumber(): string
	{
		return $this->order_number;
	}
	
	public function setOrderNumber( string $order_number ): void
	{
		$this->order_number = $order_number;
	}
	
}