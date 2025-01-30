<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_ChangeHistory;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'return_of_goods_change_history',
	database_table_name: 'returns_of_goods_change_history'
)]
abstract class Core_ReturnOfGoods_ChangeHistory extends EShopEntity_ChangeHistory {
	
	/**
	 * @var ReturnOfGoods_ChangeHistory_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: ReturnOfGoods_ChangeHistory_Item::class
	)]
	protected array $items = [];
	
	public function getReturnOfGoodsId(): int
	{
		return $this->entity_id;
	}
	
	public function setReturnOfGoods( ReturnOfGoods $return ): void
	{
		$this->entity_id = $return->getId();
		$this->setEshop( $return->getEshop() );
	}

}