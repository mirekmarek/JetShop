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
use JetApplication\Application_Service_Admin_DeliveryClasses;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_class',
	database_table_name: 'delivery_classes',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Delivery class',
	admin_manager_interface: Application_Service_Admin_DeliveryClasses::class
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
	
	public static function getScope() : array
	{
		if(isset(static::$scope[static::class])) {
			return static::$scope[static::class];
		}
		
		static::$scope[static::class] = static::dataFetchPairs(
			select: [
				'id',
				'internal_name'
			], order_by: ['id']);
		
		foreach(static::$scope[static::class] as $id=>$title) {
			static::$scope[static::class][$id] = '['.$id.'] '.$title;
		}
		
		return static::$scope[static::class];
	}
	
	
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
	
	protected static ?array $method_to_kind_map = null;
	
	protected ?array $kinds = null;
	
	
	/**
	 * @return Delivery_Kind[]
	 */
	public function getKinds() : iterable
	{
		if($this->kinds === null) {
			if(static::$method_to_kind_map === null) {
				static::$method_to_kind_map = Delivery_Method::dataFetchPairs(
					select: ['id', 'kind'],
					raw_mode: true
				);
			}
			
			$this->kinds = [];
			foreach(array_keys($this->delivery_methods) as $method_id) {
				$kind_code = static::$method_to_kind_map[$method_id] ?? null;
				if(!$kind_code) {
					continue;
				}
				
				$this->kinds[$kind_code] = Delivery_Kind::get($kind_code);
			}
		}

		return $this->kinds;
	}

	public function hasKind( string $kind ) : bool
	{
		return isset($this->getKinds()[$kind]);
	}

	public function isPersonalTakeOverOnly() : bool
	{
		foreach( $this->getKinds() as $code=>$kind ) {
			if( !$kind->isPersonalTakeoverInternal() ) {
				return false;
			}
		}

		return true;
	}

	public function isEDelivery() : bool
	{
		foreach( $this->getKinds() as $code=>$kind ) {
			if( $kind->isEDelivery() ) {
				return true;
			}
		}

		return false;
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
