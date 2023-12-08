<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithIDAndShopData_ShopData;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: '',
	database_table_name: '',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Entity_WithIDAndShopData extends DataModel
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
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
	 * @var Entity_WithIDAndShopData_ShopData[]
	 */
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];
	
	protected static ?array $scope = null;
	
	public static function exists( int $id ) : bool
	{
		return (bool)static::dataFetchCol(['id'], ['id'=>$id]);
	}
	
	
	public static function getScope() : array
	{
		if(static::$scope===null) {
			static::$scope = static::dataFetchPairs(
				select: [
					'id',
					'internal_name'
				], order_by: ['internal_name']);
		}
		return static::$scope;
	}
	
	public static function getEntityType() : string
	{
		return static::getDataModelDefinition(static::class)->getModelName();
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
	
	
	public function getInternalNotes(): string
	{
		return $this->internal_notes;
	}
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	
	
	abstract public function getShopData( ?Shops_Shop $shop = null ): Entity_WithIDAndShopData_ShopData;
}