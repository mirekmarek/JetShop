<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_Event;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_StockVerification_Event;

#[DataModel_Definition(
	name: 'whm_stock_verification_event',
	database_table_name: 'whm_stock_verification_events',
)]
abstract class Core_WarehouseManagement_StockVerification_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.WHMStockVerification.';
	
	protected static string $event_base_class_name = WarehouseManagement_StockVerification_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $varification_id = 0;
	
	protected ?WarehouseManagement_StockVerification $_varification = null;
	
	
	public function setVerification( WarehouseManagement_StockVerification $verification ) : static
	{
		$this->varification_id = $verification->getId();
		$this->_varification = $verification;
		
		return $this;
	}
	
	public function getVerificationId() : int
	{
		return $this->varification_id;
	}
	
	public function getVerification() : WarehouseManagement_StockVerification
	{
		if($this->_varification===null) {
			$this->_varification = WarehouseManagement_StockVerification::get($this->varification_id);
		}
		
		return $this->_varification;
	}
	
	
	/**
	 * @param int $entity_id
	 *
	 * @return static[]
	 */
	public static function getEventsList( int $entity_id ) : array
	{
		return static::fetch(
			[''=>[
				'varification_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
