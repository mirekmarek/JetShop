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
	name: 'delivery_method_service',
	database_table_name: 'delivery_methods_services',
	parent_model_class: Delivery_Method::class,
	N_model_class: Services_Service::class
)]
abstract class Core_Delivery_Method_Services extends DataModel_Related_MtoN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $delivery_method_code = '';

	#[DataModel_Definition(related_to: 'service.code')]
	protected string $service_code = '';

}