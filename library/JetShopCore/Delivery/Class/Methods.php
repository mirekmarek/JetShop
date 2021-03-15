<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel_Related_MtoN;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_methods_methods',
	database_table_name: 'delivery_methods_classes',
	parent_model_class: Delivery_Class::class,
	N_model_class: Delivery_Method::class
)]
abstract class Core_Delivery_Class_Methods extends DataModel_Related_MtoN
{
	#[DataModel_Definition(related_to: 'delivery_method.code')]
	protected string $method_code = '';

	#[DataModel_Definition(related_to: 'main.code')]
	protected string $class_code = '';

}