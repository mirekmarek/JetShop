<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\DataModel_Definition_Property_DataModel;
use JetApplication\Entity_Basic;
use JetApplication\Entity_HasEShopRelation_Interface;
use JetApplication\Entity_HasEShopRelation_Trait;
use JetApplication\Entity_ChangeHistory_Item;

#[DataModel_Definition]
abstract class Core_Entity_ChangeHistory extends Entity_Basic implements Entity_HasEShopRelation_Interface
{
	use Entity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $entity_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_added = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999
	)]
	protected string $comment = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $administrator = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $administrator_id = 0;
	
	/**
	 * @var Entity_ChangeHistory_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Entity_ChangeHistory_Item::class
	)]
	protected array $items = [];
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function setId( int $id ): void
	{
		$this->id = $id;
	}
	
	public function getEntityId(): int
	{
		return $this->entity_id;
	}
	
	public function getDateAdded(): ?Data_DateTime
	{
		return $this->date_added;
	}
	
	public function setDateAdded( Data_DateTime|string|null $date_added ): void
	{
		$this->date_added = Data_DateTime::catchDateTime( $date_added );
	}
	
	public function getComment(): string
	{
		return $this->comment;
	}
	
	public function setComment( string $comment ): void
	{
		$this->comment = $comment;
	}
	
	public function getAdministrator(): string
	{
		return $this->administrator;
	}
	
	public function setAdministrator( string $administrator ): void
	{
		$this->administrator = $administrator;
	}
	
	public function getAdministratorId(): int
	{
		return $this->administrator_id;
	}
	
	public function setAdministratorId( int $administrator_id ): void
	{
		$this->administrator_id = $administrator_id;
	}
	
	
	/**
	 * @param int $entity_id
	 *
	 * @return static[]
	 */
	public static function getForEntity( int $entity_id ) : array
	{
		return static::fetch(
			[''=>[
				'entity_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
	public function addChange(
		string $property,
		string $old_value,
		string $new_value
	) : void
	{
		/**
		 * @var DataModel_Definition_Property_DataModel $def
		 */
		$def = static::getDataModelDefinition()->getProperty('items');
		
		$class_name = $def->getValueDataModelClass();
		
		/**
		 * @var Entity_ChangeHistory_Item $item
		 */
		$item = new $class_name();
		$item->setProperty( $property );
		$item->setOldValue( $old_value );
		$item->setNewValue( $new_value );
		$item->setEntityId( $this->getId() );
		
		$this->items[] = $item;
	}
	
	public function hasChange( ?string $property = null ) : bool
	{
		if($property) {
			
			foreach($this->items as $item) {
				if( $item->getProperty()==$property ) {
					return true;
				}
			}
			
			return false;
		}
		
		return count($this->items)>0;
	}
	
	/**
	 * @return Entity_ChangeHistory_Item[]
	 */
	public function getChanges(): array
	{
		return $this->items;
	}
	
	
}