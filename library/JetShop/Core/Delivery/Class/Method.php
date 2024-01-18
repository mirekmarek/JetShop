<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Delivery_Class;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_methods_methods',
	database_table_name: 'delivery_methods_classes',
	parent_model_class: Delivery_Class::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Class_Method extends DataModel_Related_1toN
{
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $method_id = 0;

	#[DataModel_Definition(
		related_to: 'main.id',
		is_id: true,
		is_key: true,
	)]
	protected int $class_id = 0;

	public function getArrayKeyValue(): string
	{
		return $this->method_id;
	}

	public function getMethodId(): string
	{
		return $this->method_id;
	}

	public function setMethodId( int $method_id ): void
	{
		$this->method_id = $method_id;
	}

	public function getClassId(): int
	{
		return $this->class_id;
	}

	public function setClassId( int $class_id ): void
	{
		$this->class_id = $class_id;
	}
}