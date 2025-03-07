<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_ChangeHistory;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'money_refunds_change_history',
	database_table_name: 'money_refunds_change_history'
)]
abstract class Core_MoneyRefund_ChangeHistory extends EShopEntity_ChangeHistory {
	
	
	/**
	 * @var MoneyRefund_ChangeHistory_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: MoneyRefund_ChangeHistory_Item::class
	)]
	protected array $items = [];

	public function getMoneyRefundId(): int
	{
		return $this->entity_id;
	}
	
	public function setMoneyRefund( MoneyRefund $money_refund ): void
	{
		$this->entity_id = $money_refund->getId();
		$this->setEshop( $money_refund->getEshop() );
	}
}