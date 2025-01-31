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
use JetApplication\Complaint;
use JetApplication\Complaint_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'complaints_change_history',
	database_table_name: 'complaints_change_history'
)]
abstract class Core_Complaint_ChangeHistory extends EShopEntity_ChangeHistory {
	
	
	/**
	 * @var Complaint_ChangeHistory_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Complaint_ChangeHistory_Item::class
	)]
	protected array $items = [];

	public function getComplaintId(): int
	{
		return $this->entity_id;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->entity_id = $complaint->getId();
		$this->setEshop( $complaint->getEshop() );
	}
}