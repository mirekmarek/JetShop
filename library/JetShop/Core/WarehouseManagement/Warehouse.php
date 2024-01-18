<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\Entity_Common;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\WarehouseManagement_Warehouse_Shop;


#[DataModel_Definition(
	name: 'warehouse',
	database_table_name: 'whm_warehouses',
)]
class Core_WarehouseManagement_Warehouse extends Entity_Common
{

	/**
	 * @var Core_WarehouseManagement_Warehouse_Shop[]
	 */
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Associated shops:',
		default_value_getter_name: 'getShopKeys',
		setter_name: 'setShops',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select shop'
		],
		select_options_creator: [Shops::class, 'getScope']
	)]
	protected ?array $shops = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - street and number:'
	)]
	protected string $address_street_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - country:'
	)]
	protected string $address_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - town:'
	)]
	protected string $address_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - zip:'
	)]
	protected string $address_zip = '';

	
	public function getShopKeys() : array
	{
		if($this->shops===null) {
			$this->shops = [];
			foreach(WarehouseManagement_Warehouse_Shop::fetchInstances(['warehouse_id'=>$this->id]) as $shop) {
				$this->shops[$shop->getShopKey()] = $shop->getShopKey();
			}
		}
		
		return array_keys($this->shops);
	}
	
	public function setShops( array $shop_keys ) : void
	{
		$current = $this->getShopKeys();
		
		if(
			!array_diff($current, $shop_keys) &&
			!array_diff($shop_keys, $current)
		) {
			return;
		}
		
		WarehouseManagement_Warehouse_Shop::dataDelete(['warehouse_id'=>$this->id]);
		$this->shops = [];
		foreach($shop_keys as $shop_key) {
			$shop = Shops::get( $shop_key );
			$wh_shop = new WarehouseManagement_Warehouse_Shop();
			$wh_shop->setShop( $shop );
			$wh_shop->setWarehouseId( $this->id );
			$wh_shop->save();
			
			$this->shops[] = $wh_shop;
		}
	}

	public function hasShop( Shops_Shop $shop ) : bool
	{
		return in_array($shop->getKey(), $this->getShopKeys());
	}


	public function setAddressStreetNo( string $value ) : void
	{
		$this->address_street_no = $value;
	}

	public function getAddressStreetNo() : string
	{
		return $this->address_street_no;
	}

	public function setAddressCountry( string $value ) : void
	{
		$this->address_country = $value;
	}
	
	public function getAddressCountry() : string
	{
		return $this->address_country;
	}

	public function setAddressTown( string $value ) : void
	{
		$this->address_town = $value;
	}

	public function getAddressTown() : string
	{
		return $this->address_town;
	}

	public function setAddressZip( string $value ) : void
	{
		$this->address_zip = $value;
	}

	public function getAddressZip() : string
	{
		return $this->address_zip;
	}

}
