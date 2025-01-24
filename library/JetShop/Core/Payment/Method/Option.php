<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\DataModel;

use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Entity_WithEShopData;
use JetApplication\JetShopEntity_Definition;
use JetApplication\Payment_Method_Option_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'payment_methods_options',
	database_table_name: 'payment_methods_options',
)]
#[JetShopEntity_Definition]
abstract class Core_Payment_Method_Option extends Entity_WithEShopData implements Admin_Entity_WithEShopData_Interface {
	
	use Admin_Entity_WithEShopData_Trait;
	
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
	
	public function getEditURL( array $get_params=[] ) : string
	{
		return '';
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'icon1',
			image_title:  Tr::_('Icon 1'),
		);
		$this->defineImage(
			image_class:  'icon2',
			image_title:  Tr::_('Icon 3'),
		);
		$this->defineImage(
			image_class:  'icon3',
			image_title:  Tr::_('Icon 3'),
		);
		
	}
	
}