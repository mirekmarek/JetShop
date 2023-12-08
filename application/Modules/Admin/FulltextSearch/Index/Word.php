<?php
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
	protected string $object_class = '';

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
	
	public function getObjectClass(): string
	{
		return $this->object_class;
	}
	
	public function setObjectClass( string $object_class ): void
	{
		$this->object_class = $object_class;
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
