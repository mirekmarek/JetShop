<?php
/**
 * 
 */

namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;

use JetApplication\Entity_WithShopData;
use JetApplication\Order_Status_Kind;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Order_Status_ShopData;

/**
 *
 */
#[DataModel_Definition(
	name: 'order_status',
	database_table_name: 'order_statuses',
)]
abstract class Core_Order_Status extends Entity_WithShopData
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Kind:',
		select_options_creator: [
			Order_Status_Kind::class,
			'getScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is default'
	)]
	protected bool $is_default = false;
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Order_Status_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];
	
	
	public function getShopData( ?Shops_Shop $shop=null ) : Order_Status_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
	
	public function setKind( string $value ) : void
	{
		$this->kind = $value;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setKind( $value );
		}
	}
	
	
	public function getKindCode(): string
	{
		return $this->kind;
	}
	
	public function getKind() : ?Order_Status_Kind
	{
		return Order_Status_Kind::get( $this->kind );
	}
	
	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
	}
	

	public function isDefault(): bool
	{
		return $this->is_default;
	}
	
	public function setIsDefault( bool $is_default ): void
	{
		if( $is_default==$this->is_default ) {
			return;
		}
		
		$this->is_default = $is_default;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setIsDefault( $is_default );
		}
		
		if($is_default) {
			static::updateData(
				data:[
					'is_default' => false
				],
				where: [
					'kind' => $this->kind,
					'AND',
					'id !=' => $this->id
				]
			);
			
			Order_Status_ShopData::updateData(
				data:[
					'is_default' => false
				],
				where: [
					'kind' => $this->kind,
					'AND',
					'entity_id !=' => $this->id
				]
			);
			
		}
		
	}
	
	public static function getDefault( string $kind_code ) : ?static
	{
		return static::load([
			'kind' => $kind_code,
			'AND',
			'is_default' => true
		]);
	}
	
}
