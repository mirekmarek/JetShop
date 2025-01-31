<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;
use JetApplication\EShopEntity_ChangeHistory;

#[DataModel_Definition(
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: EShopEntity_ChangeHistory::class
)]
abstract class Core_EShopEntity_ChangeHistory_Item extends DataModel_Related_1toN {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		related_to: 'main.id',
	)]
	protected int $history_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $entity_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $property = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $old_value = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $new_value = '';
	
	public function getEntityId(): int
	{
		return $this->entity_id;
	}
	
	public function setEntityId( int $entity_id ): void
	{
		$this->entity_id = $entity_id;
	}
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public function getProperty() : string
	{
		return $this->property;
	}
	
	public function setProperty( string $property ) : void
	{
		$this->property = $property;
	}
	
	public function getOldValue() : string
	{
		return $this->old_value;
	}
	
	public function setOldValue( string $old_value ) : void
	{
		$this->old_value = $old_value;
	}
	
	public function getNewValue() : string
	{
		return $this->new_value;
	}
	
	public function setNewValue( string $new_value ) : void
	{
		$this->new_value = $new_value;
	}
	
}