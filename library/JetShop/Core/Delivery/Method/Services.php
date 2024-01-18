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


#[DataModel_Definition(
	name: 'delivery_method_service',
	database_table_name: 'delivery_methods_services',
	parent_model_class: Delivery_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Method_Services extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.id')]
	protected int $delivery_method_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected string $service_id = '';

	public function getArrayKeyValue(): string
	{
		return $this->service_id;
	}
	
	public function getDeliveryMethodId(): int
	{
		return $this->delivery_method_id;
	}
	
	public function setDeliveryMethodId( int $delivery_method_id ): void
	{
		$this->delivery_method_id = $delivery_method_id;
	}
	
	public function getServiceId(): int
	{
		return $this->service_id;
	}
	
	public function setServiceId( int $service_id ): void
	{
		$this->service_id = $service_id;
	}
}