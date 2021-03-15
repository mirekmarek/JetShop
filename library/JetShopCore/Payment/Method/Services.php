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
	name: 'payment_method_service',
	database_table_name: 'payment_methods_services',
	parent_model_class: Payment_Method::class,
	N_model_class: Services_Service::class
)]
abstract class Core_Payment_Method_Services extends DataModel_Related_MtoN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $payment_method_code = '';

	#[DataModel_Definition(related_to: 'service.code')]
	protected string $service_code = '';

}