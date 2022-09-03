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
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected string $method_code = '';

	#[DataModel_Definition(related_to: 'main.code')]
	protected string $class_code = '';

	protected Delivery_Method|null|bool $method = null;

	public function getArrayKeyValue(): string
	{
		return $this->method_code;
	}

	public function getMethodCode(): string
	{
		return $this->method_code;
	}

	public function setMethodCode( string $method_code ): void
	{
		$this->method_code = $method_code;
		$this->method = null;
	}

	public function getClassCode(): string
	{
		return $this->class_code;
	}

	public function setClassCode( string $class_code ): void
	{
		$this->class_code = $class_code;
	}

	public function getMethod() : ?Delivery_Method
	{
		if($this->method===null) {
			$this->method = Delivery_Method::get($this->method_code);
			if(!$this->method) {
				$this->method = false;
			}
		}

		return $this->method ? : null;
	}

}