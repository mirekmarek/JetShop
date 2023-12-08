<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Locale;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: '',
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Entity_WithShopRelation_ShopIsID extends DataModel
{
	protected ?Shops_Shop $_shop = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		is_id: true,
	)]
	protected string $shop_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
		is_id: true,
	)]
	protected ?Locale $locale = null;
	
	public function setShop( Shops_Shop $shop ) : void
	{
		$this->shop_code = $shop->getShopCode();
		$this->locale = $shop->getLocale();
		$this->_shop = $shop;
	}
	
	public function getShopCode() : string
	{
		return $this->shop_code;
	}
	
	public function getLocale(): ?Locale
	{
		return $this->locale;
	}
	
	public function getShop() : Shops_Shop
	{
		if(!$this->_shop) {
			$this->_shop = Shops::get( $this->getShopKey() );
		}
		
		return $this->_shop;
	}
	
	public function getShopKey() : string
	{
		return $this->shop_code.'_'.$this->locale;
	}
	
}