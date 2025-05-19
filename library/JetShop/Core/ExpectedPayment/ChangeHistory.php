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
use JetApplication\ExpectedPayment;
use JetApplication\ExpectedPayment_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'expected_payment_change_history',
	database_table_name: 'expected_payment_change_history'
)]
abstract class Core_ExpectedPayment_ChangeHistory extends EShopEntity_ChangeHistory {
	
	/**
	 * @var ExpectedPayment_ChangeHistory_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: ExpectedPayment_ChangeHistory_Item::class
	)]
	protected array $items = [];
	
	public function getExpectedPaymentId(): int
	{
		return $this->entity_id;
	}
	
	public function setExpectedPayment( ExpectedPayment $return ): void
	{
		$this->entity_id = $return->getId();
		$this->setEshop( $return->getEshop() );
	}
	
}