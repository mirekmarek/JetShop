<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_ChangeHistory;
use JetApplication\Order;
use JetApplication\Order_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'orders_change_history',
	database_table_name: 'orders_change_history'
)]
abstract class Core_Order_ChangeHistory extends Entity_ChangeHistory {
	
	/**
	 * @var Order_ChangeHistory_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Order_ChangeHistory_Item::class
	)]
	protected array $items = [];
	
	
	public function getOrderId(): int
	{
		return $this->entity_id;
	}
	
	public function setOrder( Order $order ): void
	{
		$this->entity_id = $order->getId();
		$this->setEshop( $order->getEshop() );
	}
	

}