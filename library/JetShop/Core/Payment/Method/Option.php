<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel;

use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\Payment_Method_Option_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'payment_methods_options',
	database_table_name: 'payment_methods_options',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Payment method option',
	separate_tab_form_shop_data: false,
	images: [
		'icon1' => 'Icon 1',
		'icon2' => 'Icon 2',
		'icon3' => 'Icon 3',
	]
)]
abstract class Core_Payment_Method_Option extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	EShopEntity_Admin_WithEShopData_Interface
{
	
	use EShopEntity_Admin_WithEShopData_Trait;
	use EShopEntity_WithEShopData_HasImages_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $method_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	
	/**
	 * @var Payment_Method_Option_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_Option_EShopData::class
	)]
	protected array $eshop_data = [];
	
	public function getMethodId(): int
	{
		return $this->method_id;
	}
	
	public function setMethodId( int $method_id ): void
	{
		$this->method_id = $method_id;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData($eshop)->setMethodId( $method_id );
		}
	}
	
	public function getEshopData( ?EShop $eshop=null ) : Payment_Method_Option_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData($eshop)->setPriority( $priority );
		}
		
	}
	
	
	
	public static function getListForMethod( int $method_id ) : array
	{
		$options = static::fetchInstances(['method_id'=>$method_id] );
		$options->getQuery()->setOrderBy(['priority']);
		
		$res = [];
		
		foreach($options as $opt) {
			$res[$opt->getInternalCode()] = $opt;
		}
		
		return $res;
	}
	
	public function getEditUrl( array $get_params=[] ) : string
	{
		return '';
	}
	
}