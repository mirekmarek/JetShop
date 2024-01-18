<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Field;
use Jet\Form_Definition;
use Jet\Form_Field_Select;

use JetApplication\Entity_WithShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Services_Kind;
use JetApplication\Services_Service_ShopData;

/**
 *
 */
#[DataModel_Definition(
	name: 'service',
	database_table_name: 'services',
)]
abstract class Core_Services_Service extends Entity_WithShopData
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Group:'
	)]
	protected string $group = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Kind:',
		select_options_creator: [Services_Kind::class, 'getScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';


	/**
	 * @var Services_Service_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Services_Service_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	public function getKindCode(): string
	{
		return $this->kind;
	}

	public function setKind( string $kind ): void
	{
		$this->kind = $kind;
		foreach(Shops::getList() as $shop) {
			$this->getShopData($shop)->setKind($kind);
		}
	}


	public function getKind(): ?Services_Kind
	{
		return Services_Kind::get( $this->kind );
	}

	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind?$kind->getTitle() : '?';
	}
	

	public function getShopData( ?Shops_Shop $shop=null ) : Services_Service_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	public function setGroup( string $value ) : void
	{
		$this->group = $value;
	}
	
	public function getGroup() : string
	{
		return $this->group;
	}
	
	protected static ?array $_scope = null;

	protected static function _getScope( string $kind ) : array
	{
		if(static::$_scope===null) {

			static::$_scope = [];

			foreach(Services_Kind::getScope() as $_kind=>$kind_title) {
				static::$_scope[$_kind] = [];
			}
			
			$list = static::fetchInstances();
			foreach($list as $item) {
				static::$_scope[$item->getKindCode()][$item->getId()] = $item->getInternalName();
			}
		}

		return static::$_scope[$kind];
	}
	
	public static function getDeliveryServicesScope() : array
	{
		return static::_getScope( Services_Kind::KIND_DELIVERY );
	}
	
	public static function getPaymentServicesScope() : array
	{
		return static::_getScope( Services_Kind::KIND_PAYMENT );
	}
	
	public static function getOtherServicesScope() : array
	{
		return static::_getScope( Services_Kind::KIND_OTHER );
	}
	
}
