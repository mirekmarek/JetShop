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

use JetApplication\Delivery_Method;
use JetApplication\Delivery_Class;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method_classes',
	database_table_name: 'delivery_methods_classes',
	parent_model_class: Delivery_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Method_Class extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $method_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected string $class_code = '';

	protected Delivery_Class|null|bool $class = null;

	public function getArrayKeyValue(): string
	{
		return $this->class_code;
	}

	public function getMethodCode(): string
	{
		return $this->method_code;
	}

	public function setMethodCode( string $method_code ): void
	{
		$this->method_code = $method_code;
	}

	public function getClassCode(): string
	{
		return $this->class_code;
	}

	public function setClassCode( string $class_code ): void
	{
		$this->class_code = $class_code;
		$this->class = null;
	}
	
	public function getClass() : ?Delivery_Class
	{
		if($this->class===null) {
			$this->class = Delivery_Class::get($this->class_code);
			if(!$this->class) {
				$this->class = false;
			}
		}
		
		return $this->class ? : null;
	}

}