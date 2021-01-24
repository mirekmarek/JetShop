<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;

#[DataModel_Definition(
	name: '',
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Fulltext_Index_Word extends DataModel {

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_id: true,
		is_key: true
	)]
	protected string $shop_id = '';

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

	public function getShopId() : string
	{
		return $this->shop_id;
	}

	public function setShopId( string $shop_id ) : void
	{
		$this->shop_id = $shop_id;
	}

	public function getObjectId() : int
	{
		return $this->object_id;
	}

	public function setObjectId( int $object_id ) : void
	{
		$this->object_id = $object_id;
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
