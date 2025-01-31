<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\FulltextSearch;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;


#[DataModel_Definition(
	name: 'word',
	database_table_name: 'fulltext_internal_word',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Index_Word extends DataModel {
	
	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_id: true,
		is_key: true,
		max_len: 50
	)]
	protected string $entity_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $object_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
		is_id: true
	)]
	protected string $word = '';

	public function getObjectId() : int
	{
		return $this->object_id;
	}

	public function setObjectId( int $object_id ) : void
	{
		$this->object_id = $object_id;
	}
	
	public function getEntitytype(): string
	{
		return $this->entity_type;
	}
	
	public function setEntitytype( string $entity_type ): void
	{
		$this->entity_type = $entity_type;
	}

	public function getWord() : string
	{
		return $this->word;
	}

	public function setWord( string $word ) : void
	{
		$this->word = $word;
	}
}
