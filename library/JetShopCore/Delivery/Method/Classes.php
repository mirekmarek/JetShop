<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek.2m@gmail.com>
 *
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 */
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel_Related_MtoN;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method_classes',
	database_table_name: 'delivery_methods_classes',
	parent_model_class: Delivery_Method::class,
	N_model_class: Delivery_Class::class
)]
abstract class Core_Delivery_Method_Classes extends DataModel_Related_MtoN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $method_code = '';

	#[DataModel_Definition(related_to: 'delivery_class.code')]
	protected string $class_code = '';

}