<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Delivery_Class_Method;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Kind;
use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_Definition;
use JetApplication\Admin_Managers_DeliveryClasses;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_class',
	database_table_name: 'delivery_classes',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Delivery class',
	admin_manager_interface: Admin_Managers_DeliveryClasses::class
)]
abstract class Core_Delivery_Class extends EShopEntity_Common implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is default',
	)]
	protected bool $is_default = false;
	
	
	/**
	 * @var Delivery_Class_Method[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Class_Method::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Methods:',
		default_value_getter_name: 'getDeliveryMethodIds',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select delivery method'
		],
		select_options_creator: [Delivery_Method::class, 'getScope'],
		
	)]
	protected array $delivery_methods = [];
	
	public static function getDefault() : ?static
	{
		return static::load(['is_default'=>true]);
	}
	
	public function isIsDefault(): bool
	{
		return $this->is_default;
	}
	
	public function setIsDefault( bool $is_default ): void
	{
		$this->is_default = $is_default;
		if($is_default) {
			static::updateData(['is_default'=>false], [
				'id !=' => $this->id
			]);
		}
	}

	
	
	/**
	 * @return Delivery_Kind[]
	 */
	public function getKinds() : iterable
	{
		$kinds = [];
		foreach(array_keys($this->delivery_methods) as $method_id) {
			$method = Delivery_Method::load( $method_id );
			if($method) {
				$kind = $method->getKind();
				
				$kinds[$kind->getCode()] = $kind;
			}
		}

		return $kinds;
	}

	public function hasKind( string $kind ) : bool
	{
		return isset($this->getKinds()[$kind]);
	}

	public function isPersonalTakeOverOnly() : bool
	{
		foreach( $this->getKinds() as $code=>$kind ) {
			if($code!=Delivery_Kind::PERSONAL_TAKEOVER_INTERNAL) {
				return false;
			}
		}

		return true;
	}

	public function isEDelivery() : bool
	{
		foreach( $this->getKinds() as $code=>$kind ) {
			if($code!=Delivery_Kind::E_DELIVERY) {
				return false;
			}
		}

		return true;
	}
	
	public function setDeliveryMethods( array $ids ) : void
	{
		foreach($this->delivery_methods as $r) {
			if(!in_array($r->getMethodId(), $ids)) {
				$r->delete();
				unset($this->delivery_methods[$r->getMethodId()]);
			}
		}

		foreach( $ids as $id ) {
			if( !Delivery_Method::exists( $id ) ) {
				continue;
			}

			if(!isset($this->delivery_methods[$id])) {
				
				$new_item = new Delivery_Class_Method();
				$new_item->setClassId( $this->id );
				$new_item->setMethodId( $id );

				$this->delivery_methods[$id] = $new_item;
				$new_item->save();
			}
		}
	}

	public function getDeliveryMethodIds() : array
	{
		return array_keys($this->delivery_methods);
	}
	
}
