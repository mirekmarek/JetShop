<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;



#[DataModel_Definition(
	name: 'products_parameters_info_not_avl',
	database_table_name: 'products_parameters_info_not_avl',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
)]
abstract class Core_Product_Parameter_InfoNotAvl extends DataModel
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $property_id = 0;
	
	
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}
	
	public function getPropertyId() : int
	{
		return $this->property_id;
	}
	
	public function setPropertyId( int $property_id ) : void
	{
		$this->property_id = $property_id;
	}
	

	
	/**
	 * @param int $product_id
	 * @return static[]
	 */
	public static function get( int $product_id ) : array
	{
		$data = static::fetch(
			['products_parameters_info_not_avl'=>[
				'product_id' => $product_id
			]]
		);
		
		$map = [];
		
		foreach($data as $item) {
			$property_id = $item->property_id;
			$map[$property_id] = $item;
		}
		
		return $map;
	}
	
}