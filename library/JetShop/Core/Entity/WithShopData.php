<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Definition_Property_DataModel;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_Basic;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition]
abstract class Core_Entity_WithShopData extends Entity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:'
	)]
	protected string $internal_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal code:',
		error_messages: [
			'code_used' => 'This code is already used'
		]
	)]
	protected string $internal_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Internal notes:'
	)]
	protected string $internal_notes = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $is_active = false;
	
	
	/**
	 * @var Entity_WithShopData_ShopData[]
	 */
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];
	
	protected static array $scope = [];
	
	
	public function __construct()
	{
		$this->checkShopData();
	}
	
	public function setId( int $id ) : void
	{
		$this->checkShopData();
		$this->id = $id;
	}
	
	
	public function afterLoad() : void
	{
		$this->checkShopData();
	}
	
	public function checkShopData() : void
	{
		/**
		 * @var DataModel_Definition_Property_DataModel $def
		 */
		$def = static::getDataModelDefinition()->getProperty('shop_data');
		
		$shop_data_class = $def->getValueDataModelClass();
		
		foreach( Shops::getList() as $shop ) {
			$key = $shop->getKey();
			
			if(!isset($this->shop_data[$key])) {
				
				$sh = new $shop_data_class();
				
				$this->shop_data[$key] = $sh;
			}
			
			$this->shop_data[$key]->setShop( $shop );
		}
	}
	
	public static function internalCodeUsed( string $internal_code, int $skip_id=0 ) : bool
	{
		return (bool)static::dataFetchCol(['id'], [
			'internal_code'=>$internal_code,
			'AND',
			'id !=' => $skip_id
		]);
	}
	
	public static function getScope() : array
	{
		if(isset(static::$scope[static::class])) {
			return static::$scope[static::class];
		}
		
		static::$scope[static::class] = static::dataFetchPairs(
				select: [
					'id',
					'internal_name'
				], order_by: ['internal_name']);
		
		return static::$scope[static::class];
	}
	
	public static function getOptionsScope() : array
	{
		return [0=>'']+static::getScope();
	}
	
	
	
	public function isActive() : bool
	{
		return $this->is_active;
	}
	
	
	public function getId() : int
	{
		return $this->id;
	}
	
	
	public function getInternalName(): string
	{
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	
	public function getInternalCode() : string
	{
		return $this->internal_code;
	}
	
	public function setInternalCode( string $internal_code ) : void
	{
		$this->internal_code = $internal_code;
		foreach($this->shop_data as $sd) {
			$sd->setInternalCode( $this->is_active );
		}
		
	}
	
	
	public function getInternalNotes(): string
	{
		return $this->internal_notes;
	}
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	
	
	public function activate() : void
	{
		$this->is_active = true;
		static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
		
		foreach($this->shop_data as $sd) {
			$sd->_setEntityIsActive( $this->is_active );
		}
	}
	
	public function deactivate() : void
	{
		$this->is_active = false;
		static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
		
		foreach($this->shop_data as $sd) {
			$sd->_setEntityIsActive( $this->is_active );
		}
	}
	
	abstract public function getShopData( ?Shops_Shop $shop = null ): Entity_WithShopData_ShopData;
}