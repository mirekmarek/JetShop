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

use JetApplication\Payment_Method;
use JetApplication\Services_Service;

/**
 *
 */
#[DataModel_Definition(
	name: 'payment_method_service',
	database_table_name: 'payment_methods_services',
	parent_model_class: Payment_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Payment_Method_Services extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.id')]
	protected string $payment_method_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected string $service_id = '';

	protected Services_Service|null|bool $service = null;

	public function getArrayKeyValue(): string
	{
		return $this->service_id;
	}

	public function getPaymentMethodId(): int
	{
		return $this->payment_method_id;
	}

	public function setPaymentMethodId( int $payment_method_id ): void
	{
		$this->payment_method_id = $payment_method_id;
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