<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Customer_Address;
use JetApplication\EShopEntity_Address;

/**
 *
 */
#[DataModel_Definition(
	name: 'customer_address',
	database_table_name: 'customers_addresses',
)]
abstract class Core_Customer_Address extends EShopEntity_Address
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $customer_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $is_default = false;

	/**
	 * @return Customer_Address[]
	 */
	public static function getListForCustomer( int $customer_id ) : iterable
	{
		$where = [
			'customer_id' => $customer_id
		];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}
	
	public function setCustomerId( int $value ) : void
	{
		$this->customer_id = $value;
	}
	
	public function getCustomerId() : int
	{
		return $this->customer_id;
	}
	
	public function setIsDefault() : void
	{
		$this->is_default = true;
		
		static::updateData(
			[
				'is_default' => true
			],
			[
				'id' => $this->getId()
			]
		);
		
		static::updateData(
			[
				'is_default' => false
			],
			[
				'customer_id' => $this->getCustomerId(),
				'AND',
				'id !=' => $this->getId(),
			]
		);
		
	}

	public function isDefault() : bool
	{
		return $this->is_default;
	}

}
