<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivationByTimePlan_Interface;
use JetApplication\EShopEntity_HasActivationByTimePlan_Trait;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasInternalParams_Interface;
use JetApplication\EShopEntity_HasInternalParams_Trait;
use JetApplication\EShopEntity_HasProductsRelation_Interface;
use JetApplication\EShopEntity_HasProductsRelation_Trait;
use JetApplication\EShopEntity_Marketing;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Product_Relation;
use JetApplication\Product_RelevantRelation;

#[DataModel_Definition]
abstract class Core_EShopEntity_Marketing extends EShopEntity_Basic implements
	EShopEntity_HasInternalParams_Interface,
	EShopEntity_HasEShopRelation_Interface,
	EShopEntity_HasGet_Interface,
	EShopEntity_HasActivationByTimePlan_Interface,
	EShopEntity_HasProductsRelation_Interface
{
	use EShopEntity_HasEShopRelation_Trait;
	use EShopEntity_HasInternalParams_Trait;
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasActivationByTimePlan_Trait;
	use EShopEntity_HasProductsRelation_Trait;
	
	
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'e-shop',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Invalid value',
			Form_Field::ERROR_CODE_EMPTY         => 'Invalid value'
		],
		select_options_creator: [
			EShops::class,
			'getScope'
		],
		default_value_getter_name: 'getEshopKey',
		creator: ['this', 'eshopFieldCreator']
	)]
	protected ?EShop $eshop = null;
	
	public function eshopFieldCreator( Form_Field $field ) : Form_Field
	{
		$field->setFieldValueCatcher( function( string $eshop_key ) {
			$eshop = EShops::get( $eshop_key );
			$this->setEshop( $eshop );
		} );
		
		return $field;
	}
	
	public static function getActiveByInternalCode( string $internal_code, ?EShop $eshop=null ) : ?static
	{
		$where = static::getActiveQueryWhere( $eshop );
		$where[] = 'AND';
		$where['internal_code'] = $internal_code;
		
		return static::load( $where );
	}
	
	/**
	 * @param array $ids
	 * @param EShop|null $eshop
	 * @param array|string|null $order_by
	 * @return static[]
	 */
	public static function getActiveList( array $ids, ?EShop $eshop=null, array|string|null $order_by = null ) : array
	{
		if(!$ids) {
			return [];
		}
		
		$where = static::getActiveQueryWhere( $eshop );
		$where[] = 'AND';
		$where['id'] = $ids;
		
		$_res =  static::fetch(
			where_per_model: [ ''=>$where],
			order_by: $order_by,
			item_key_generator: function( EShopEntity_Marketing $item ) : int {
				return $item->getId();
			}
		);
		
		if($order_by) {
			return $_res;
		}
		
		$res = [];
		
		foreach($ids as $id) {
			if(isset($_res[$id])) {
				$res[$id] = $_res[$id];
			}
		}
		
		return $res;
	}
	
	/**
	 * @param EShop|null $eshop
	 * @param array|string|null $order_by
	 * @return static[]
	 */
	public static function getAllActive( ?EShop $eshop=null, array|string|null $order_by = null ) : array
	{
		$where = static::getActiveQueryWhere( $eshop );
		
		return static::fetch(
			where_per_model: [ ''=>$where],
			order_by: $order_by,
			item_key_generator: function( EShopEntity_Marketing $item ) : int {
				return $item->getId();
			}
		);
	}
	
	
	public static function getActiveQueryWhere( ?EShop $eshop=null ) : array
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$where = [];
		$where[] = $eshop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'is_active' => true
		];
		
		return $where;
	}
	
	
	public static function getNonActiveQueryWhere( ?EShop $eshop=null ) : array
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$where = [];
		$where[] = $eshop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'is_active' => false
		];
		
		return $where;
	}
	
	public function afterDelete(): void
	{
		parent::afterDelete();
		Product_RelevantRelation::removeAll( $this );
		Product_Relation::removeAll( $this );
	}
	
}