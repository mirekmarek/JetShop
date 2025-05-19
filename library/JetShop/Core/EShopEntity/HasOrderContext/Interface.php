<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Order;

interface Core_EShopEntity_HasOrderContext_Interface
{
	public function setOrder( Order $order ) : void;
	public function getOrderId(): int;
	public function setOrderId( int $order_id ): void;
	public function getOrderNumber(): string;
	public function setOrderNumber( string $order_number ): void;
}