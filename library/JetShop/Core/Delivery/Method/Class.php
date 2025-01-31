<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Delivery_Method;

#[DataModel_Definition(
	name: 'delivery_method_classes',
	database_table_name: 'delivery_methods_classes',
	parent_model_class: Delivery_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Method_Class extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.id')]
	protected int $method_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected int $class_id = 0;

	public function getArrayKeyValue(): string
	{
		return $this->class_id;
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