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
	name: 'payment_method_delivery_method',
	database_table_name: 'delivery_methods_payment_methods',
	parent_model_class: Payment_Method::class,
	N_model_class: Delivery_Method::class
)]
abstract class Core_Payment_Method_DeliveryMethods extends DataModel_Related_MtoN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $payment_method_code = '';

	#[DataModel_Definition(related_to: 'delivery_method.code')]
	protected string $delivery_method_code = '';

}